<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Book;
use App\Hospital;
use App\User;
use App\Review;
use Illuminate\Support\Facades\Hash;

class ReviewController extends Controller
{
    public function addReview(Request $request){
        $newReview = new Review();
        $newReview->user_id = $request->input('user_id');
        $newReview->faskes_id = $request->input('faskes_id');
        $newReview->rating = $request->input('rating');
        $newReview->review = $request->input('review');
        $newReview->save();

        return response()->json([
            'status'  => 200,
            'data'    => $newReview,
            'message' => 'Registration Success']);
    }
}
