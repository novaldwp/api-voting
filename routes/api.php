<?php

use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\CandidateController;
use App\Http\Controllers\API\v1\DashboardController;
use App\Http\Controllers\API\v1\ElectionController;
use App\Http\Controllers\API\v1\UserController;
use App\Http\Controllers\API\v1\VotingController;
use App\Models\Election;
use App\Models\User;
use App\Models\Voting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
   return $request->user();
});

Route::prefix('v1')->group(function() {
    Route::get('/test', function() {
        $participants = User::whereRole('voter')->get();
        $elections = Election::with(['candidates'])->get();

        foreach($participants as $participant) {
            foreach($elections as $election) {
                $arrRand = [0, 1, 3, 4];
                if (array_rand($arrRand) != 0) {
                    $candidates_id  = [];
                    $candidates     = $election->candidates;
                    for ($i = 0; $i < count($candidates); $i++) {
                        array_push($candidates_id, $candidates[$i]->id);
                    }
                    $data = [
                        'user_id' => $participant->id,
                        'candidate_id' => $candidates_id[array_rand($candidates_id)],
                        'election_id' => $election->id
                    ];

                    Voting::create($data);
                }
            }
        }
    });
    Route::get('/testfake', function() {
        $faker = Faker\Factory::create();

        for ($i = 0; $i <= 50; $i++)
        {
            $data = [
                'name' => $faker->name,
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'password' => bcrypt("password"),
                'role' => "voter"
            ];

            User::create($data);
        }
    });
    Route::group(['middleware' => 'auth:sanctum'], function() {
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/users/paginate', [UserController::class, 'paginate']);
        Route::resource('/users', UserController::class)->except(['create', 'edit']);
        Route::get('/elections/getElectionWithTotalParticipant', [ElectionController::class, 'getElectionWithTotalParticipant']);
        Route::get('/elections/getVotingRecapitulation/{election_id}', [ElectionController::class, 'getVotingRecapitulation']);
        Route::get('/elections/paginate', [ElectionController::class, 'paginate']);
        Route::resource('/elections', ElectionController::class)->except(['create', 'edit']);
        Route::get('/candidates/paginate', [CandidateController::class, 'paginate']);
        Route::resource('/candidates', CandidateController::class)->except(['create', 'edit']);
        Route::get('/votings/paginate', [VotingController::class, 'paginate']);
        Route::resource('/votings', VotingController::class)->except(['create', 'edit']);
    });

    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

});
