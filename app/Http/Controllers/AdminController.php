<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Quiz;
use App\Models\Mcq;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    //
    function adminlogin(Request $request){
        // return $request->input();
        $validation = $request->validate([
            "name" => "required",
            "password" => "required",
        ]);
        $admin = Admin::where([
            ['name' ,"=", $request->name],
            ['password' ,"=", $request->password]
        ])->first();

        if(!$admin){
            $validation = $request->validate([
            "user" => "required",
        ],[
            "user.required" => "User does not exist"
        ]);
        }
        
        Session::put('admin', $admin);
        return redirect('dashboard');
        // return view('admin', ['name'=>$admin->name]);
    }

    function dashboard(){
        $admin = Session::get('admin');
        if($admin){
            $users = User::orderBy('id', 'desc')->paginate(5);
            return view('admin', ['name'=>$admin->name, 'users'=>$users]);
        } else{
            return redirect('admin-login');
        }
        
    }
    function categories(){
        $categories = Category::get();
        $admin = Session::get('admin');
        if($admin){
            return view('categories', ['name'=>$admin->name, "categories"=>$categories]);
        } else{
            return redirect('admin-login');
        }
        
    }
    function logout(){
        Session::forget('admin');
        return redirect('admin-login');
    }
    function addCategory(Request $request){
        // return "Done";
        $validatio = $request->validate([
            "category_name" => 'required | min:5 | unique:categories,name'
        ]);
        $admin = Session::get('admin');
        $category = new Category();
        // return $request;
        $category->name = $request->category_name;
        $category->creator = $admin->name;
        if($category->save()){
            Session::flash('category_name', 'Successs: Category ' . $request->category_name. ' Added.');
        }
        return redirect('admin-categories');
    }
    function deleteCategory($id){
        // return $id;
        $delete = Category::find($id)->delete();
        if($delete){
            Session::flash('category_delete', 'Successs: Category Deleted.');
            return redirect('admin-categories');
        }
    }
    function addQuiz()
{
    $categories = Category::get();
    $admin = Session::get('admin');
    $totalMCQs = 0;

    if ($admin) {
        $quizName = request('quiz_name');
        $categoryID = request('category_id');

        if ($quizName && $categoryID && !Session::has('quizDetails')) {
            $quiz = new Quiz();
            $quiz->name = $quizName;
            $quiz->category_id = $categoryID;
            if ($quiz->save()) {
                Session::put('quizDetails', $quiz);
            }
        } else {
            $quiz = Session::get('quizDetails');
            if ($quiz) {
                $totalMCQs = Mcq::where('quiz_id', $quiz->id)->count();
            }
        }

        return view('add-quiz', [
            'name' => $admin->name,
            'categories' => $categories,
            'totalMCQs' => $totalMCQs
        ]);
    } else {
        return redirect('admin-login');
    }
}

    function addMCQS(Request $request)
{
    $request->validate([
        "question" => "required|min:5",
        "option_a" => "required",
        "option_b" => "required",
        "option_c" => "required",
        "option_d" => "required",
        "correct_ans" => "required"
    ]);

    $quiz = Session::get('quizDetails');
    $admin = Session::get('admin');

    if (!$quiz || !$admin) {
        return redirect('/add-quiz')->with('error', 'Quiz session expired. Please create quiz again.');
    }

    $mcq = new Mcq();
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
        if ($request->submit == "add-more") {
            // ✅ Redirect to add-quiz so totalMCQs updates live
            return redirect('/add-quiz')->with('success', 'MCQ Added Successfully!');
        } else {
            // ✅ End quiz flow
            Session::forget('quizDetails');
            return redirect('/admin-categories')->with('success', 'Quiz Completed Successfully!');
        }
    } else {
        return redirect('/add-quiz')->with('error', 'Something went wrong. Please try again.');
    }
}

    function endQuiz(){
        Session::forget('quizDetails');
        return redirect('/add-quiz');
    }

    function showQuiz($id, $quizName){
        $admin = Session::get('admin');
        $mcqs =  Mcq::where('quiz_id', $id)->get();
        if($admin){
            return view('show-quiz', ['name'=>$admin->name, "mcqs"=>$mcqs, "quizName"=>$quizName]);
        } else{
            return redirect('admin-login');
        }
    }
    function quizList($id, $category){
         $admin = Session::get('admin');
        if($admin){
        $quizData =  Quiz::where('category_id', $id)->get();
            return view('quiz-list', ['name'=>$admin->name, "quizData"=>$quizData, "category"=>$category]);
        } else{
            return redirect('admin-login');
        }
    }
}
