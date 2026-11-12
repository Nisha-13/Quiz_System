<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Mcq;
use App\Models\MCQ_Record;
use App\Models\Quiz;
use App\Models\Record;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    /**
     * Platform-wide counters shown in the admin sidebar and dashboard.
     */
    private function stats(): array
    {
        return [
            'users' => User::count(),
            'categories' => Category::count(),
            'quizzes' => Quiz::count(),
            'mcqs' => Mcq::count(),
            'attempts' => Record::count(),
        ];
    }

    public function dashboard()
    {
        $admin = Session::get('admin');
        if (! $admin) {
            return redirect('login');
        }
        $stats = $this->stats();
        $users = User::orderBy('id', 'desc')->paginate(5);

        return view('admin', ['name' => $admin->name, 'users' => $users, 'stats' => $stats]);
    }

    public function categories()
    {
        $categories = Category::withCount('quizzes')->withCount('mcqs')->get();
        $admin = Session::get('admin');
        if (! $admin) {
            return redirect('login');
        }

        return view('categories', ['name' => $admin->name, 'categories' => $categories, 'stats' => $this->stats()]);
    }

    public function addCategory(Request $request)
    {
        $validation = $request->validate([
            'category_name' => 'required|min:5|unique:categories,name',
        ]);
        $admin = Session::get('admin');
        $category = new Category;
        $category->name = $request->category_name;
        $category->creator = $admin->name;
        if ($category->save()) {
            Session::flash('category_name', 'Category "'.$request->category_name.'" Added Successfully.');
        }

        return redirect('admin-categories');
    }

    public function deleteCategory($id)
    {
        $category = Category::find($id);
        if (! $category) {
            return redirect('admin-categories')->with('category_delete', 'Category not found.');
        }
        $category->delete();
        Session::flash('category_delete', 'Category deleted successfully.');

        return redirect('admin-categories');
    }

    public function showAddQuizForm()
    {
        $admin = Session::get('admin');
        if (! $admin) {
            return redirect('login');
        }

        $categories = Category::get();
        $quiz = Session::get('quizDetails');

        $totalMCQs = 0;
        if ($quiz instanceof Quiz) {
            $totalMCQs = Mcq::where('quiz_id', $quiz->id)->count();
        }

        return view('add-quiz', [
            'name' => $admin->name,
            'categories' => $categories,
            'totalMCQs' => $totalMCQs,
            'stats' => $this->stats(),
        ]);
    }

    public function addQuiz(Request $request)
    {
        $admin = Session::get('admin');
        if (! $admin) {
            return redirect('login');
        }

        if (! $request->isMethod('post')) {
            return back();
        }

        $request->validate([
            'quiz_name' => 'required|string|min:3',
            'category_id' => 'required|exists:categories,id',
        ]);

        $quizName = $request->input('quiz_name');
        $categoryID = $request->input('category_id');

        $quiz = Quiz::where('name', $quizName)
            ->where('category_id', $categoryID)
            ->first();

        if ($quiz) {
            Session::put('quizDetails', $quiz);
            $totalMCQs = Mcq::where('quiz_id', $quiz->id)->count();
            Session::flash('quiz_info', "Quiz '$quizName' already exists in this category. Total MCQs: $totalMCQs");
        } else {
            $quiz = new Quiz;
            $quiz->name = $quizName;
            $quiz->category_id = $categoryID;
            $quiz->save();

            Session::put('quizDetails', $quiz);
            $totalMCQs = 0;
            Session::flash('success', "Quiz '$quizName' created. Now add your MCQs.");
        }

        return redirect('add-quiz');
    }

    public function addMCQS(Request $request)
    {
        $request->validate([
            'question' => 'required|min:5',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'correct_ans' => 'required',
        ]);

        $quiz = Session::get('quizDetails');
        if ($request->filled('quiz_id')) {
            $fromShow = Quiz::find($request->input('quiz_id'));
            if ($fromShow) {
                $quiz = $fromShow;
            }
        }

        $admin = Session::get('admin');
        if (! $quiz || ! $admin) {
            return redirect('/admin-categories')->with('error', 'Quiz session expired. Please create the quiz again.');
        }
        $mcq = new Mcq;
        $mcq->question = $request->question;
        $mcq->option_a = $request->option_a;
        $mcq->option_b = $request->option_b;
        $mcq->option_c = $request->option_c;
        $mcq->option_d = $request->option_d;
        $mcq->correct_ans = $request->correct_ans;
        $mcq->admin_id = $admin->id;
        $mcq->quiz_id = $quiz->id;
        $mcq->category_id = $quiz->category_id;
        if ($mcq->save()) {
            if ($request->submit == 'add-more') {
                if ($request->filled('quiz_id')) {
                    return redirect('show-quiz/'.$quiz->id.'/'.$quiz->name)->with('success', 'Question added to "'.$quiz->name.'" successfully.');
                }

                return redirect('/add-quiz')->with('success', 'MCQ Added Successfully!');
            } else {
                Session::forget('quizDetails');
                if ($request->filled('quiz_id')) {
                    return redirect('show-quiz/'.$quiz->id.'/'.$quiz->name)->with('success', 'Question added. Quiz is live and playable.');
                }

                return redirect('/admin-categories')->with('success', 'Quiz Completed & Published Successfully!');
            }
        } else {
            return redirect('show-quiz/'.$quiz->id.'/'.$quiz->name)->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function endQuiz()
    {
        Session::forget('quizDetails');

        return redirect('/admin-categories')->with('success', 'Quiz saved and closed.');
    }

    public function showQuiz($quizId, $quizName)
    {
        $admin = Session::get('admin');
        $quiz = Quiz::with('category')->find($quizId);
        if (! $quiz) {
            return redirect('admin-categories')->with('error', 'Quiz not found.');
        }
        $mcqs = Mcq::where('quiz_id', $quizId)->get();

        return view('show-quiz', ['name' => $admin->name, 'mcqs' => $mcqs, 'quizName' => $quizName,
            'quiz' => $quiz,
            'stats' => $this->stats(),
        ]);
    }

    public function deleteQuiz($id)
    {
        $quiz = Quiz::find($id);
        if (! $quiz) {
            return back()->with('error', 'Quiz not found.');
        }
        Mcq::where('quiz_id', $id)->delete();
        Record::where('quiz_id', $id)->delete();
        $quiz->delete();

        return back()->with('success', 'Quiz deleted successfully.');
    }

    public function deleteMCQ($id)
    {
        $mcq = Mcq::find($id);
        if (! $mcq) {
            return back()->with('error', 'Question not found.');
        }
        $mcq->delete();

        return back()->with('success', 'Question deleted successfully.');
    }

    public function quizList($id, $category)
    {
        $admin = Session::get('admin');
        if (! $admin) {
            return redirect('login');
        }
        $quizData = Quiz::withCount('mcqs')->where('category_id', $id)->get();

        return view('quiz-list', ['name' => $admin->name, 'quizData' => $quizData, 'category' => $category, 'categoryId' => $id, 'stats' => $this->stats()]);
    }

    /**
     * Management page: every registered user.
     */
    public function users()
    {
        $admin = Session::get('admin');
        if (! $admin) {
            return redirect('login');
        }

        $users = User::withCount('records')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin-users', ['name' => $admin->name, 'users' => $users, 'stats' => $this->stats()]);
    }

    /**
     * Management page: every quiz.
     */
    public function quizzes()
    {
        $admin = Session::get('admin');
        if (! $admin) {
            return redirect('login');
        }

        $quizzes = Quiz::with('category')
            ->withCount('mcqs')
            ->withCount('records')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin-quizzes', ['name' => $admin->name, 'quizzes' => $quizzes, 'stats' => $this->stats()]);
    }

    /**
     * Management page: every question of every quiz.
     */
    public function questions()
    {
        $admin = Session::get('admin');
        if (! $admin) {
            return redirect('login');
        }

        $questions = Mcq::with('quiz')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin-questions', ['name' => $admin->name, 'questions' => $questions, 'stats' => $this->stats()]);
    }

    /**
     * Management page: every quiz attempt.
     */
    public function attempts()
    {
        $admin = Session::get('admin');
        if (! $admin) {
            return redirect('login');
        }

        $records = Record::with(['quiz', 'user'])
            ->orderBy('records.id', 'desc')
            ->paginate(15);

        $scores = MCQ_Record::selectRaw('record_id, COUNT(*) as total, SUM(is_correct) as correct')
            ->whereIn('record_id', $records->pluck('id'))
            ->groupBy('record_id')
            ->get()
            ->keyBy('record_id');

        return view('admin-attempts', ['name' => $admin->name, 'records' => $records, 'scores' => $scores, 'stats' => $this->stats()]);
    }
}
