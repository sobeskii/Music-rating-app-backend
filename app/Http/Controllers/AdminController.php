<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\ConfirmRequest;
use App\Http\Requests\Admin\UserBanRequest;
use App\Http\Requests\Admin\UserMuteRequest;
use App\Http\Requests\Rule\RulePostRequest;
use App\Interfaces\RatingRepositoryInterface;
use App\Interfaces\RuleRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\ModerationRule;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\Helper;

class AdminController extends Controller
{
    /**
     * @var AdminService
     */
    private $adminService;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var RatingRepositoryInterface
     */
    private $ratingRepository;
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @param AdminService $adminService
     * @param UserRepositoryInterface $userRepository
     * @param RatingRepositoryInterface $ratingRepository
     * @param RuleRepositoryInterface $ruleRepository
     */
    public function __construct(AdminService $adminService,
                                UserRepositoryInterface $userRepository,
                                RatingRepositoryInterface $ratingRepository,
                                RuleRepositoryInterface $ruleRepository)
    {
        $this->middleware(['json']);
        $this->adminService = $adminService;
        $this->userRepository = $userRepository;
        $this->ratingRepository = $ratingRepository;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function users(Request $request): JsonResponse
    {
        $term       =   ($request->term) != null ? $request->term : '';
        $perPage    =   (($request->perPage) != null && $request->perPage <= 20 && $request->perPage >= 5) ? (int)$request->perPage : 15;

        $users =    $this->userRepository->getUsers($term,$perPage);

        return response()->json([
            'users' => $users,
            'request_items' => [
                'perPage' => $perPage,
                'term' => $term,
            ],
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function flagged_reviews(Request $request): JsonResponse
    {
        $term       =   ($request->term) != null ? $request->term : '';
        $perPage    =   (($request->perPage) != null && $request->perPage <= 20 && $request->perPage >= 5) ? (int)$request->perPage : 15;

        $reviews =    $this->ratingRepository->getFlaggedReviews($term,$perPage);

        return response()->json([
            'reviews' => $reviews,
            'request_items' => [
                'perPage' => $perPage,
                'term' => $term,
            ],
        ]);
    }

    /**
     * @param UserBanRequest $request
     * @return JsonResponse
     */
    public function ban_user(UserBanRequest $request): JsonResponse
    {
        $user = $this->adminService->handleBanAction($request->validated(), 1);

        return response()->json([
            'data' => ['user' => $user],
            'success' => ['User has been banned!']
        ], 200);
    }

    /**
     * @param UserBanRequest $request
     * @return JsonResponse
     */
    public function unban_user(UserBanRequest $request): JsonResponse
    {
        $user = $this->adminService->handleBanAction($request->validated(), 0);

        return response()->json([
            'data' => ['user' => $user],
            'success' => ['User has been unbanned!']
        ], 200);
    }

    /**
     * @param UserMuteRequest $request
     * @return JsonResponse
     */
    public function mute_user(UserMuteRequest $request): JsonResponse
    {
        $user = $this->adminService->handleMuteAction($request->validated(),1);

        return response()->json([
            'data' => ['user' => $user],
            'success' => ['User has been muted!']
        ], 200);
    }

    /**
     * @param UserMuteRequest $request
     * @return JsonResponse
     */
    public function unmute_user(Request $request): JsonResponse
    {
        $user = $this->adminService->handleMuteAction(
            [   'user_id' =>$request->user_id,
                'time' => $request->time,
                'mute_reason' => null],0);

        return response()->json([
            'data' => ['user' => $user],
            'success' => ['User has been unmuted!']
        ], 200);
    }

    /**
     * @param ConfirmRequest $request
     * @return JsonResponse
     */
    public function revert_flag(ConfirmRequest $request): JsonResponse
    {
        $rating = $this->adminService->handleConfirmAction($request->validated());

        return response()->json([
            'data' => ['rating' => $rating],
            'success' => ['Decision reverted!']
        ],200);
    }

    /**
     * @return JsonResponse
     */
    public function rules(): JsonResponse
    {
        $rules = $this->ruleRepository->getRules();

        return response()->json([
            'data' => ['rules' => $rules],
        ],200);
    }

    /**
     * @param RulePostRequest $request
     * @return JsonResponse
     */
    public function create_rule(RulePostRequest $request): JsonResponse
    {
        $rule = $this->adminService->handleRuleCreate($request->validated());

        return response()->json([
            'data' => ['rule' => $rule],
            'success' => ['Rule has been created!']
        ],200);
    }

    /**
     * @param ModerationRule $rule
     * @param RulePostRequest $request
     * @return JsonResponse
     */
    public function edit_rule(ModerationRule $rule, RulePostRequest $request): JsonResponse
    {
        $rule = $this->adminService->handleRuleEdit($rule,$request->validated());

        return response()->json([
            'data' => ['rule' => $rule],
            'success' => ['Rule has been edited!']
        ],200);
    }

    /**
     * @param ModerationRule $rule
     * @return JsonResponse
     */
    public function delete_rule(ModerationRule $rule): JsonResponse
    {
        $rule = $this->adminService->handleRuleDelete($rule);

        return response()->json([
            'data' => ['rule' => $rule],
            'success' => ['Rule has been deleted!']
        ],200);
    }
}
