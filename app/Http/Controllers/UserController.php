<?php

namespace App\Http\Controllers;

use App\Http\Responses\FileDownloadResponse;
use App\Mail\UserForgotPassword;
use App\Mail\VerifyUser;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Mcq;
use App\Models\MCQ_Record;
use App\Models\Quiz;
use App\Models\Record;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Minimum score (in percent) required to earn a certificate.
     */
    public const CERTIFICATE_THRESHOLD = 70;

    public function welcome()
    {
        $categories = Category::withCount('quizzes')->orderBy('quizzes_count', 'desc')->take(6)->get();
        $quizData = Quiz::with(['category'])->withCount('mcqs')->orderBy('id', 'desc')->take(6)->get();

        return view('welcome', ['categories' => $categories, 'quizData' => $quizData]);
    }

    public function categories()
    {
        $categories = Category::withCount('quizzes')->orderBy('quizzes_count', 'desc')->paginate(9);

        return view('categories-list', ['categories' => $categories]);
    }

    public function userQuizList($id, $category)
    {
        $quizData = Quiz::withCount('mcqs')->where('category_id', $id)->orderBy('id', 'desc')->paginate(9);

        return view('user-quiz-list', ['quizData' => $quizData, 'category' => $category, 'categoryId' => $id]);
    }

    public function startQuiz($id, $name)
    {
        $quiz = Quiz::find($id);
        if (! $quiz) {
            return redirect('/categories')->with('message-error', 'Quiz not found.');
        }

        $mcqs = Mcq::where('quiz_id', $id)->get();
        $quizCount = $mcqs->count();

        if ($quizCount === 0) {
            return redirect()->back()->with('no_mcq', 'Oops! This quiz has no MCQs yet.');
        }

        Session::put('firstMCQ', $mcqs->first());

        $user = Session::get('user');
        $attempt = null;
        if ($user) {
            $attempt = Record::where('user_id', $user->id)
                ->where('quiz_id', $id)
                ->where('status', 1)
                ->latest('id')
                ->first();
        }

        return view('start-quiz', [
            'quizName' => $name ?: $quiz->name,
            'quizCount' => $quizCount,
            'passMark' => self::CERTIFICATE_THRESHOLD,
            'attempt' => $attempt,
        ]);
    }

    public function userSignup(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required | min:3',
                'email' => 'required | email | unique:users',
                'password' => 'required | min:3 | confirmed',
                'password_confirmation' => 'required',
            ]);
        } catch (ValidationException $exception) {
            throw $this->withConfirmationError($exception);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'active' => User::STATUS_UNVERIFIED,
        ]);

        Session::put('pending_verification_email', $user->email);

        $sent = $this->sendVerificationEmail($user);

        return redirect('verify-email')
            ->with('email', $user->email)
            ->with('message-success', $sent
                ? 'Account created! We sent a verification link to '.$user->email.'.'
                : 'Account created, but we could not send the verification email. Please request a new link below.');
    }

    /**
     * Send the verification link to a freshly registered account.
     */
    private function sendVerificationEmail(User $user): bool
    {
        $link = url('/verify-user/'.Crypt::encryptString($user->email));

        try {
            Mail::to($user->email)->send(new VerifyUser($link));

            return true;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }

    /**
     * Mirror a "password confirmation" mismatch onto the confirmation field so
     * the message renders directly underneath the input that caused it.
     */
    private function withConfirmationError(ValidationException $exception): ValidationException
    {
        $errors = $exception->errors();

        foreach ($errors['password'] ?? [] as $message) {
            if (str_contains($message, 'confirmation')) {
                $errors['password_confirmation'][] = $message;
            }
        }

        return ValidationException::withMessages($errors);
    }

    public function userLogout()
    {
        Session::forget('user');

        return redirect('/')->with('message-success', 'Logged out successfully.');
    }

    public function userSignupQuiz()
    {
        Session::put('quiz_url', url()->previous());

        return view('user-signup');
    }

    public function userLoginViewRedirect()
    {
        return redirect('login');
    }

    public function loginView()
    {
        if (Session::has('admin')) {
            return redirect('dashboard');
        }
        if (Session::has('user')) {
            return redirect('/');
        }

        return view('login');
    }

    public function login(Request $request)
    {
        $validate = $request->validate([
            'account' => 'required',
            'password' => 'required',
        ]);

        // 1) Admin account
        $admin = Admin::where('name', $request->account)->first();
        if ($admin && Hash::check($request->password, $admin->password)) {
            Session::put('admin', $admin);

            return redirect('dashboard')->with('success', 'Welcome back, '.$admin->name.'!');
        }

        // 2) Regular user account
        $user = User::where('email', $request->account)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            // Unverified accounts are not allowed to sign in.
            if (! $user->isVerified()) {
                Session::put('pending_verification_email', $user->email);

                return redirect('verify-email')
                    ->with('email', $user->email)
                    ->with('message-error', 'Please verify your email address before signing in. We sent you a verification link.');
            }

            Session::put('user', $user);
            Session::forget('pending_verification_email');
            if (Session::has('quiz_url')) {
                $url = Session::get('quiz_url');
                Session::forget('quiz_url');

                return redirect($url)->with('message-success', 'Welcome back, '.$user->name.'!');
            }

            return redirect('/')->with('message-success', 'Welcome back, '.$user->name.'!');
        }

        return redirect('login')
            ->with('message-error', 'Invalid credentials, please check your email/username and password.')
            ->withInput($request->only('account'));
    }

    public function loginQuiz()
    {
        Session::put('quiz_url', url()->previous());

        return redirect('login');
    }

    public function logoutSession()
    {
        Session::forget('user');
        Session::forget('admin');

        return redirect('login')->with('message-success', 'Logged out successfully.');
    }

    public function mcq($id, $name)
    {
        $user = Session::get('user');
        if (! $user) {
            return redirect('login')->with('message-error', 'Please login to attempt the quiz.');
        }

        $firstMCQ = Session::get('firstMCQ');
        $mcqData = $firstMCQ ? Mcq::find($firstMCQ->id) : Mcq::find($id);
        if (! $mcqData) {
            return redirect('/categories')->with('message-error', 'Quiz not found.');
        }

        $record = $this->findOrCreateAttempt($user->id, $mcqData->quiz_id);
        if (! $record) {
            return redirect('/')->with('message-error', 'Something went wrong, please try again.');
        }

        $answeredIds = MCQ_Record::where('record_id', $record->id)->pluck('mcq_id')->toArray();
        $nextMcq = Mcq::where('quiz_id', $record->quiz_id)
            ->whereNotIn('id', $answeredIds)
            ->orderBy('id')
            ->first();

        if (! $nextMcq) {
            return $this->attemptResult($record, $name);
        }

        $currentQuiz = [
            'totalMcq' => Mcq::where('quiz_id', $record->quiz_id)->count(),
            'currentMcq' => count($answeredIds) + 1,
            'quizName' => $name,
            'quizId' => $record->quiz_id,
            'recordId' => $record->id,
        ];
        Session::put('currentQuiz', $currentQuiz);
        Session::put('firstMCQ', $nextMcq);

        return view('mcq-page', ['quizName' => $name, 'mcqData' => $nextMcq]);
    }

    /**
     * Reuse the user's in-progress attempt for a quiz, or open a new one.
     */
    private function findOrCreateAttempt($userId, $quizId)
    {
        $record = Record::where('user_id', $userId)
            ->where('quiz_id', $quizId)
            ->where('status', 1)
            ->latest('id')
            ->first();

        if ($record) {
            return $record;
        }

        $record = new Record;
        $record->quiz_id = $quizId;
        $record->user_id = $userId;
        $record->status = 1;

        return $record->save() ? $record : null;
    }

    /**
     * Build the answer-review page for a finished attempt.
     */
    private function attemptResult($record, $quizName = null)
    {
        $resultData = MCQ_Record::withMCQ()->where('record_id', $record->id)->get();
        $correctAnswers = MCQ_Record::where([
            ['record_id', '=', $record->id],
            ['is_correct', '=', 1],
        ])->count();

        $record->status = 2;
        $record->save();

        $quiz = Quiz::find($record->quiz_id);

        return view('result-page', [
            'resultData' => $resultData,
            'correctAnswers' => $correctAnswers,
            'quizName' => str_replace('-', ' ', $quizName ?: ($quiz->name ?? 'Quiz')),
            'passMark' => self::CERTIFICATE_THRESHOLD,
        ]);
    }

    public function submitAndNext(Request $request, $id)
    {
        $currentQuiz = Session::get('currentQuiz');
        if (! $currentQuiz) {
            return redirect('/')->with('message-error', 'Quiz session expired, please start again.');
        }

        $user = Session::get('user');
        if (! $user) {
            return redirect('login')->with('message-error', 'Please login to attempt the quiz.');
        }

        $record = Record::find($currentQuiz['recordId']);
        if (! $record || $record->user_id != $user->id) {
            return redirect('/')->with('message-error', 'Quiz session expired, please start again.');
        }

        $mcq = Mcq::where('quiz_id', $currentQuiz['quizId'])->where('id', $id)->first();
        if (! $mcq) {
            return redirect('/')->with('message-error', 'That question does not belong to this quiz.');
        }

        $validated = $request->validate([
            'option' => 'required|in:a,b,c,d',
        ]);

        $isExist = MCQ_Record::where([
            ['record_id', '=', $record->id],
            ['mcq_id', '=', $id],
        ])->count();

        if ($isExist < 1) {
            $mcq_record = new MCQ_Record;
            $mcq_record->record_id = $record->id;
            $mcq_record->user_id = $user->id;
            $mcq_record->mcq_id = $id;
            $mcq_record->select_answer = $validated['option'];
            $mcq_record->is_correct = ($validated['option'] === $mcq->correct_ans) ? 1 : 0;

            if (! $mcq_record->save()) {
                return back()->with('message-error', 'Something went wrong, please try again.');
            }
        }

        $answeredIds = MCQ_Record::where('record_id', $record->id)->pluck('mcq_id')->toArray();
        $nextMcq = Mcq::where('quiz_id', $currentQuiz['quizId'])
            ->whereNotIn('id', $answeredIds)
            ->orderBy('id')
            ->first();

        if ($nextMcq) {
            $currentQuiz['currentMcq'] = count($answeredIds) + 1;
            Session::put('currentQuiz', $currentQuiz);
            Session::put('firstMCQ', $nextMcq);

            return view('mcq-page', ['quizName' => $currentQuiz['quizName'], 'mcqData' => $nextMcq]);
        }

        return $this->attemptResult($record, $currentQuiz['quizName']);
    }

    public function userDetails()
    {
        $user = Session::get('user');
        $quizRecord = Record::withQuiz()
            ->where('user_id', $user->id)
            ->orderBy('records.id', 'desc')
            ->get();

        $recordIds = $quizRecord->pluck('id')->toArray();
        $allScores = [];
        if ($recordIds) {
            $rows = MCQ_Record::selectRaw('record_id, COUNT(*) as total, SUM(is_correct) as correct')
                ->whereIn('record_id', $recordIds)
                ->groupBy('record_id')
                ->get();
            foreach ($rows as $row) {
                $total = (int) $row->total;
                $correct = (int) $row->correct;
                $allScores[$row->record_id] = [
                    'correct' => $correct,
                    'total' => $total,
                    'pct' => $total > 0 ? round($correct / $total * 100) : 0,
                ];
            }
        }

        return view('user-details', [
            'quizRecord' => $quizRecord,
            'scores' => $allScores,
            'userName' => $user->name,
            'passMark' => self::CERTIFICATE_THRESHOLD,
        ]);
    }

    public function searchQuiz(Request $request)
    {
        $search = $request->search;
        $categories = Category::withCount('quizzes')
            ->where('name', 'like', '%'.$search.'%')
            ->get();
        $quizzes = Quiz::with('category')->withCount('mcqs')
            ->where('name', 'like', '%'.$search.'%')
            ->get();

        return view('quiz-search', ['categories' => $categories, 'quizzes' => $quizzes, 'quiz' => $request->search]);
    }

    public function verifyUser($email)
    {
        try {
            $verifyEmail = Crypt::decryptString($email);
        } catch (\Throwable $e) {
            return redirect('login')->with('message-error', 'Invalid verification link.');
        }

        $user = User::where('email', $verifyEmail)->first();
        if (! $user) {
            return redirect('login')->with('message-error', 'Verification link is invalid or user not found.');
        }

        if (! $user->isVerified()) {
            $user->active = User::STATUS_VERIFIED;
            $user->email_verified_at = now();
            $user->save();
        }

        // The link only proves the email address — the user still signs in
        // with their password.
        Session::forget('pending_verification_email');

        return redirect('login')->with('message-success', 'Your account has been verified. Please sign in.');
    }

    /**
     * Show the "check your inbox" screen.
     */
    public function verifyEmailView()
    {
        $email = Session::get('pending_verification_email');

        return view('verify-email', ['email' => $email]);
    }

    /**
     * Send a fresh verification link to an account that has not been verified yet.
     */
    public function resendVerificationEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required | email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return back()->with('message-error', 'We could not find an account with that email address.');
        }

        if ($user->isVerified()) {
            return redirect('login')->with('message-success', 'That account is already verified. Please sign in.');
        }

        $sent = $this->sendVerificationEmail($user);

        Session::put('pending_verification_email', $user->email);

        return redirect('verify-email')
            ->with('email', $user->email)
            ->with($sent ? 'message-success' : 'message-error', $sent
                ? 'A new verification link is on its way to '.$user->email.'.'
                : 'We could not send the email right now. Please try again in a moment.');
    }

    public function userForgotPassword(Request $request)
    {
        $validate = $request->validate([
            'email' => 'required | email',
        ]);
        $link = Crypt::encryptString($request->email);
        $link = url('/user-forgot-password/'.$link);
        try {
            Mail::to($request->email)->send(new UserForgotPassword($link));

            return redirect('/')->with('message-success', 'Please check your email to set a new password.');
        } catch (\Throwable $e) {
            return back()->with('message-error', 'Could not send the reset email. Please try again later.');
        }
    }

    public function userResetForgotPassword($email)
    {
        $orgEmail = Crypt::decryptString($email);

        return view('user-set-forgot-password', ['email' => $orgEmail]);
    }

    public function userSetForgotPassword(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required | min:3 | confirmed',
                'password_confirmation' => 'required',
            ]);
        } catch (ValidationException $exception) {
            throw $this->withConfirmationError($exception);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            if ($user->save()) {
                return redirect('login')->with('message-success', 'New password is set, please login with your new password.');
            }
        }

        return back()->with('message-error', 'Account not found, please try again.');
    }

    public function resumeQuiz($id)
    {
        $user = Session::get('user');
        $record = Record::find($id);

        if (! $record || ! $user || $record->user_id != $user->id) {
            return redirect('user-details')->with('message-error', 'Attempt not found.');
        }
        if ($record->status == 2) {
            return redirect('certificate/'.$record->id);
        }

        $quiz = Quiz::find($record->quiz_id);
        if (! $quiz) {
            return redirect('user-details')->with('message-error', 'The quiz for this attempt no longer exists.');
        }

        $answeredIds = MCQ_Record::where('record_id', $record->id)->pluck('mcq_id')->toArray();
        $nextMcq = Mcq::where('quiz_id', $record->quiz_id)
            ->whereNotIn('id', $answeredIds)
            ->orderBy('id')
            ->first();

        if (! $nextMcq) {
            return $this->attemptResult($record, $quiz->name);
        }

        $currentQuiz = [
            'totalMcq' => Mcq::where('quiz_id', $record->quiz_id)->count(),
            'currentMcq' => count($answeredIds) + 1,
            'quizName' => $quiz->name,
            'quizId' => $record->quiz_id,
            'recordId' => $record->id,
        ];
        Session::put('currentQuiz', $currentQuiz);
        Session::put('firstMCQ', $nextMcq);

        return view('mcq-page', ['quizName' => $quiz->name, 'mcqData' => $nextMcq]);
    }

    /**
     * Percentage of questions answered correctly for a given attempt.
     */
    private function attemptScore($recordId)
    {
        $correct = MCQ_Record::where([['record_id', $recordId], ['is_correct', 1]])->count();
        $total = MCQ_Record::where('record_id', $recordId)->count();

        return ['correct' => $correct, 'total' => $total, 'pct' => $total > 0 ? round($correct / $total * 100) : 0];
    }

    /**
     * Resolve the attempt a certificate should be issued for.
     *
     * @return array{0: ?Record, 1: ?string} [record, error message]
     */
    private function loadCertificateRecord($id)
    {
        $user = Session::get('user');
        if (! $user) {
            return [null, 'Please login to view your certificate.'];
        }

        if ($id) {
            $record = Record::find($id);
        } else {
            $currentQuiz = Session::get('currentQuiz');
            if (! $currentQuiz) {
                return [null, 'Certificates are available for completed quizzes only.'];
            }
            $record = Record::where('user_id', $user->id)
                ->where('quiz_id', $currentQuiz['quizId'])
                ->where('status', 2)
                ->latest()
                ->first();
        }

        if (! $record || $record->user_id != $user->id) {
            return [null, 'Attempt not found.'];
        }
        if ($record->status != 2) {
            return [null, 'Certificates are available for completed quizzes only.'];
        }

        $score = $this->attemptScore($record->id);
        if ($score['total'] > 0 && $score['pct'] < self::CERTIFICATE_THRESHOLD) {
            return [null, 'You scored '.$score['pct'].'%. You need at least '
                .self::CERTIFICATE_THRESHOLD.'% to earn a certificate.'];
        }

        return [$record, null];
    }

    private function certificateData($record)
    {
        $quiz = Quiz::find($record->quiz_id);
        $score = $this->attemptScore($record->id);

        return [
            'quiz' => str_replace('-', ' ', $quiz->name ?? 'Quiz'),
            'name' => Session::get('user')->name,
            'id' => $record->id,
            'correct' => $score['correct'],
            'total' => $score['total'],
            'score' => $score['pct'],
            'date' => $record->updated_at ?? now(),
        ];
    }

    public function certificate($id = null)
    {
        [$record, $error] = $this->loadCertificateRecord($id);
        if (! $record) {
            return redirect('user-details')->with('message-error', $error);
        }

        return response()
            ->view('certificate', ['data' => $this->certificateData($record)])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Stream the certificate of a finished attempt as a PDF file.
     *
     * The optional $file segment only exists so the download URL ends with
     * ".pdf"; the real file name is always derived from the attempt.
     */
    public function downloadCertificate($id = null, $file = null)
    {
        [$record, $error] = $this->loadCertificateRecord($id);
        if (! $record) {
            return redirect('user-details')->with('message-error', $error);
        }

        $html = view('download-certificate', [
            'data' => $this->certificateData($record),
            'certificateCss' => $this->certificateCss(),
        ])->render();

        $fontDir = storage_path('app/fonts');
        if (! is_dir($fontDir)) {
            mkdir($fontDir, 0755, true);
        }

        $options = new \Dompdf\Options;
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Helvetica');
        $options->set('fontDir', $fontDir);
        $options->set('fontCache', $fontDir);
        $options->set('chroot', base_path());

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setBasePath(public_path());
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        $filename = 'certificate-quizsphere-'.str_pad($record->id, 5, '0', STR_PAD_LEFT).'.pdf';

        return FileDownloadResponse::make(request(), $dompdf->output(), $filename, 'application/pdf');
    }

    /**
     * Styles of the certificate card, inlined into the PDF template.
     *
     * The very same stylesheets are used by the certificate page, so the
     * exported PDF looks exactly like the certificate shown on screen. The
     * font and image paths are rewritten to local files, which makes the
     * renderer embed those fonts and images in the generated document.
     */
    private function certificateCss(): string
    {
        $css = '';

        foreach (['certificate-fonts.css', 'certificate.css'] as $file) {
            $css .= file_get_contents(public_path('css/'.$file))."\n";
        }

        foreach (['fonts', 'images'] as $folder) {
            $css = str_replace(
                "url('/$folder/",
                "url('".$this->localFileUrl(public_path($folder)).'/',
                $css
            );
        }

        return $css;
    }

    /**
     * Absolute path of a local file as a URI the PDF renderer understands.
     */
    private function localFileUrl(string $path): string
    {
        return 'file://'.str_replace('\\', '/', $path);
    }
}
