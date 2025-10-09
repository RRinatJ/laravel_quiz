<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuizRequest;
use Illuminate\Http\Request;
use App\Models\Quiz;
use Inertia\Inertia;

class QuizController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Quiz/QuizList', [
            'quizzes' => Quiz::query()
                ->select('id', 'title', 'is_work', 'image', 'timer_count')
                ->paginate()
                ->withQueryString(),
            'message' => session('message'),
            'error' => session('error'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Quiz/QuizForm');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuizRequest $request)
    {
        /** @var Request $request */
        $uploaded_image = $request->hasFile('uploaded_image') ? $request->file('uploaded_image') : null;

        $quizValidated = $request->validated();
        unset($quizValidated['uploaded_image']);
        
        $createQuiz = Quiz::create($quizValidated);

        if ($createQuiz) {
           
            if(is_null($uploaded_image) === false){ 
                $createQuiz->image = $uploaded_image->store('images', 'public');                
                $createQuiz->save();
            }  

            return to_route('quiz.index')->with(['message'=> 'Quiz Created Successfully']);
        }

        return abort(500);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quiz $quiz)
    {
        return Inertia::render('Quiz/QuizForm', [
            'quiz' => $quiz, 
            'message' => session('message'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreQuizRequest $request, Quiz $quiz)
    {
        /** @var Request $request */
        $uploaded_image = $request->hasFile('uploaded_image') ? $request->file('uploaded_image') : null;

        $quizValidated = $request->validated();
        unset($quizValidated['uploaded_image']);
        
        $updateQuiz = $quiz->update($quizValidated);
        if ($updateQuiz) {            
            if(is_null($uploaded_image) === false){ 
                $quiz->image = $uploaded_image->store('images', 'public');                
                $quiz->save();
            }           

            return redirect()->route('quiz.edit', $quiz->id)->with(['message'=> 'Quiz Updated Successfully']);
        }

        return abort(500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $quiz)
    {
        try {
            $quiz->delete();
            return redirect()->route('quiz.index')->with('message', 'Quiz Deleted Successfully');
        } catch (\Exception $e) {
            return redirect()->route('quiz.index')->with('error', 'Failed to delete the quiz');
        }
    }
}
