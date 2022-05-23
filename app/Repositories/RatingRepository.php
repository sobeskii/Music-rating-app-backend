<?php

namespace App\Repositories;

use App\Interfaces\RatingRepositoryInterface;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RatingRepository implements RatingRepositoryInterface
{
    private $ratings = [ 0.5,1,1.5,2,2.5,3,3.5,4,4.5,5];

    /**
     * @param $releaseId
     * @return mixed
     */
    public function getReleaseRatings($releaseId)
    {
        return Rating::where( 'release_id' , '=' ,$releaseId );
    }

    /**
     * @param $artistId
     * @return mixed
     */
    public function getArtistsReleaseRatings($artistId)
    {
        return Rating::where( 'artist_id' , '=' ,$artistId );
    }

    /**
     * @param $releaseId
     * @return mixed
     */
    public function getRatingCount($releaseId)
    {
        return $this->getReleaseRatings($releaseId)->count();
    }

    /**
     * @param $releaseId
     * @return float
     */
    public function getRatingAverage($releaseId): float
    {
        $average = $this->getReleaseRatings($releaseId)->avg('rating');
        return ($average == null) ?    0   :   $average;
    }

    /**
     * @return mixed
     */
    public function getBestRatedReleaseIds($query)
    {
        $available_types = ['single','album'];
        $available_sorts = ['rating'=>'average','rating_count'=>'count'];

        $q = Rating::select(DB::raw('user_ratings.release_id,AVG(user_ratings.rating) as average,COUNT(user_ratings.release_id) as count'))
            ->whereHas('release',function ($q) use ($available_types,$available_sorts,$query){
                if ((isset($query['sort_by']) && array_key_exists($query['sort_by'], $available_sorts)) &&
                    (isset($query['release_date'])) &&
                    (isset($query['type']) && in_array($query['type'], $available_types))) {
                    if(!is_array($query['release_date'])) {
                        $q->whereRaw('year(STR_TO_DATE(release_date,"%Y-%m-%d")) = ' . $query['release_date']);
                    } else {
                        $q->whereRaw('year(STR_TO_DATE(release_date,"%Y-%m-%d")) >=' . $query['release_date'][0].' AND year(STR_TO_DATE(release_date,"%Y-%m-%d")) <='. $query['release_date'][1]);
                    }
                    $q->where('album_type', '=', $query['type']);
                } else if ((isset($query['release_date'])) &&
                    (isset($query['type']) && in_array($query['type'], $available_types)) && !isset($query['sort_by'])) {
                    if(!is_array($query['release_date'])) {
                        $q->whereRaw('year(STR_TO_DATE(release_date,"%Y-%m-%d")) = ' . $query['release_date']);
                    } else {
                        $q->whereRaw('year(STR_TO_DATE(release_date,"%Y-%m-%d")) >=' . $query['release_date'][0].' AND year(STR_TO_DATE(release_date,"%Y-%m-%d")) <='. $query['release_date'][1]);
                    }                    $q->where('album_type', '=', $query['type']);
                } else if ((isset($query['sort_by']) && array_key_exists($query['sort_by'], $available_sorts)) &&
                    isset($query['release_date']) && !isset($query['type'])) {
                    if(!is_array($query['release_date'])) {
                        $q->whereRaw('year(STR_TO_DATE(release_date,"%Y-%m-%d")) = ' . $query['release_date']);
                    } else {
                        $q->whereRaw('year(STR_TO_DATE(release_date,"%Y-%m-%d")) >=' . $query['release_date'][0].' AND year(STR_TO_DATE(release_date,"%Y-%m-%d")) <='. $query['release_date'][1]);
                    }
                } else if ((isset($query['sort_by']) && array_key_exists($query['sort_by'], $available_sorts) &&
                        (isset($query['type']) && in_array($query['type'], $available_types))) && !isset($query['release_date'])) {
                    $q->where('album_type', '=', $query['type']);
                } else if (isset($query['release_date']) && !isset($query['sort_by']) && !isset($query['type'])) {
                    if(!is_array($query['release_date'])) {
                        $q->whereRaw('year(STR_TO_DATE(release_date,"%Y-%m-%d")) = ' . $query['release_date']);
                    } else {
                        $q->whereRaw('year(STR_TO_DATE(release_date,"%Y-%m-%d")) >=' . $query['release_date'][0].' AND year(STR_TO_DATE(release_date,"%Y-%m-%d")) <='. $query['release_date'][1]);
                    }                } else if ((isset($query['type']) && in_array($query['type'], $available_types)) && !isset($query['sort_by']) && !isset($query['release_date'])) {
                    $q->where('album_type', '=', $query['type']);
                }
            })
            ->with('release.artist')
            ->groupBy('release_id');

        if ((isset($query['sort_by']) && array_key_exists($query['sort_by'], $available_sorts)) &&
            (isset($query['release_date'])) &&
            (isset($query['type']) && in_array($query['type'], $available_types))) {
            $q->orderBy($available_sorts[$query['sort_by']], 'DESC');
        }  else if ((isset($query['sort_by']) && array_key_exists($query['sort_by'], $available_sorts)) &&
            (isset($query['release_date'])) && !isset($query['type'])) {
            $q->orderBy($available_sorts[$query['sort_by']], 'DESC');
        } else if ((isset($query['sort_by']) && array_key_exists($query['sort_by'], $available_sorts) &&
                (isset($query['type']) && in_array($query['type'], $available_types))) && !isset($query['release_date'])) {
            $q->orderBy($available_sorts[$query['sort_by']], 'DESC');
        } else if ((isset($query['sort_by']) && array_key_exists($query['sort_by'], $available_sorts)) && !isset($query['release_date']) && !isset($query['type'])) {
            $q->orderBy($available_sorts[$query['sort_by']], 'DESC');
        }
        else{
            $q->orderBy('average', 'DESC')->orderBy('count', 'DESC');
        }

        return $q;
    }

    /**
     * @param $releaseId
     * @return array
     */
    public function getReleaseRatingDistribution($releaseId): array
    {
        $counts = Rating::select(DB::raw('user_ratings.rating as rating, COUNT(user_ratings.rating) as rating_count'))
            ->where('release_id','=',$releaseId)
            ->groupBy('rating')->get();

        $counts = $counts->keyBy((function ($item) {
            return (string)$item['rating'];
        }));

        $arr = [];

        foreach ($this->ratings as $key => $rating) {
            $arr[$key] = 0;
            foreach ($counts as $c_keys => $count){
                if($rating == $c_keys){
                    $arr[$key] = $count->rating_count;
                }
            }
        }

        return $arr;
    }

    /**
     * @param $user
     * @return array
     */
    public function getUserRatingDistribution($user): array
    {
        $counts = Rating::select(DB::raw('user_ratings.rating as rating, COUNT(user_ratings.rating) as rating_count'))
            ->where('user_id','=',$user->id)
            ->groupBy('rating')->get();

        $counts = $counts->keyBy((function ($item) {
            return (string)$item['rating'];
        }));

        $arr = [];

        foreach ($this->ratings as $key => $rating) {
            $arr[$key] = 0;
            foreach ($counts as $c_keys => $count){
                if($rating == $c_keys){
                    $arr[$key] = $count->rating_count;
                }
            }
        }
        return $arr;
    }


    /**
     * @param $releaseId
     * @return mixed
     */
    public function getReviews($releaseId)
    {
        return $this->getReleaseRatings($releaseId)->computeRatingInfo()->with(['user' => function ($q) {
            $q->select('id', 'name','mute_reason','muted_until');
        }])->withCount(['flaggedReviewReasons as isFlagged' => function($q){
        }])->withCasts(['isFlagged'=>'boolean'])->where('review', '!=', null)->orderBy('isFlagged','desc');;

    }

    /**
     * @param string $term
     * @param int $perPage
     * @return mixed
     */
    public function getFlaggedReviews(string $term, int $perPage)
    {
        return (strlen($term) > 0) ? Rating::where('review', 'like', '%' . $term . '%')
            ->has('flaggedReviewReasons', '>', 0)
            ->with(['user', 'flaggedReviewReasons'])
            ->withCount('flaggedReviewReasons')
            ->orderBy('flagged_review_reasons_count', 'desc')
            ->paginate($perPage) :
            Rating::has('flaggedReviewReasons', '>', 0)
                ->with(['user', 'flaggedReviewReasons','flaggedReviewReasons.moderation_rule'])
                ->withCount('flaggedReviewReasons')
                ->orderBy('flagged_review_reasons_count', 'desc')
                ->paginate($perPage);
    }

    /**
     * @return mixed
     */
    public function getReviewsUserLiked(User $user)
    {
        return Rating::computeRatingInfo($user->id)->whereHas('likes',function($q) use($user){
            $q->where('user_id','=',$user->id);
        })->with(['user','release','release.artist'])
            ->withCount(['flaggedReviewReasons as isFlagged' => function($q){
            }])->withCasts(['isFlagged'=>'boolean'])->where('review', '!=', null)->orderBy('isFlagged','desc')
            ->orderBy('created_at','desc');
    }

    /**
     * @return mixed
     */
    public function getUserRatings(User $user)
    {
        return Rating::computeRatingInfo()->where('user_id',$user->id)
            ->with(['user','release','release.artist'])
            ->withCount(['flaggedReviewReasons as isFlagged' => function($q){
            }])->withCasts(['isFlagged'=>'boolean'])
            ->orderBy('isFlagged','desc')
            ->orderBy('created_at','desc');
    }

    /**
     * @return mixed
     */
    public function getUserReleaseRatings(string $release_id)
    {
        return Rating::computeRatingInfo()
            ->where('release_id',$release_id)
            ->with('user')
            ->orderBy('created_at','desc');
    }

    /**
     * @return mixed
     */
    public function getUserRatingStatsByDecade(User $user){
        return DB::select('select LEFT(yr, 3) as decade , sum(cnt) as count, user_id from
                                    (select count(spotify_id) as cnt, year(STR_TO_DATE(releases.release_date,"%Y-%m-%d")) as yr, user_ratings.user_id as user_id from releases
                                    right join user_ratings on releases.spotify_id = user_ratings.release_id
                                    group by user_id, yr)
                                    as T1
                                    where user_id = '.$user->id.'
                                    group by user_id, LEFT(yr,3);'
                        );
    }
}
