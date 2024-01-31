<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
//use App\Joeys;
use App\JoeyRouteLocations;
use App\SprintTaskHistory;


class JoeyRoutes extends Model
{
    /**
     * Table name.
     *
     * @var array
     */
    public $table = 'joey_routes';

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "id",
        "joey_id",
        "created_at",
        "updated_at",
        "deleted_at",
        "date",
        "total_travel_time",
        "total_distance",
    ];


    /**
     * Get joey data.
     */
    /*public function Joey()
    {
        return $this->belongsTo(Joeys::class,'joey_id', 'id');
    }*/

    /**
     * Get joey routs locations.
     */
    public function RouteLocarions()
    {
        return $this->hasMany( JoeyRouteLocations::class,'route_id', 'id');
    }

    /**
     * Get Total Tasks Ids in this Route .
     */
    public function GetAllTaskIds()
    {
        // gating current routs tasks ids
        return $this->RouteLocarions()->NotDeleted()->pluck('task_id')->toArray();
    }

    /**
     * Get Total Numbers Of Order Drops in this Route .
     */
    public function TotalOrderDropsCount()
    {
        return $this->RouteLocarions()->count();
    }

    /**
     * Get Total Numbers Of Orders Completed in this Route .
     */
    public function TotalOrderDropsCompletedCount()
    {
        // gating current routs tasks ids
        $tasks_ids = $this->GetAllTaskIds();

        return SprintTaskHistory::whereIn('sprint__tasks_id',$tasks_ids)->where('status_id',17)->Active()->count();
    }

    /**
     * Get Total Numbers Of Orders Picked in this Route .
     */
    public function TotalOrderPickedCount()
    {
        // gating current routs tasks ids
        $tasks_ids = $this->GetAllTaskIds();
        return SprintTaskHistory::whereIn('sprint__tasks_id',$tasks_ids)->where('status_id',121)->Active()->count();
    }

    /**
     * Get Total Numbers Of Orders Unattempted in this Route .
     */
    public function TotalOrderReturnCount()
    {
        // gating current routs tasks ids
        $tasks_ids = $this->GetAllTaskIds();
        return SprintTaskHistory::whereIn('sprint__tasks_id',$tasks_ids)->whereIn('status_id',[106,111,105,131])->Active()->count();
    }

    /**
     * Get Total Numbers Of Orders Unattempted in this Route .
     */
    public function TotalOrderUnattemptedCount()
    {
        return $this->TotalOrderPickedCount() - ($this->TotalOrderDropsCompletedCount() + $this->TotalOrderReturnCount());
    }


    /**
     * Get Total Time Of Orders FirstDropScan in this Route .
     */
    public function FirstDropScan()
    {
        // gating current routs tasks ids
        $tasks_ids = $this->GetAllTaskIds();
        $data = SprintTaskHistory::whereIn('sprint__tasks_id',$tasks_ids)->where('status_id',17)->orderBy('created_at','asc')->Active()->first()->toArray();
        return $data['created_at'];

    }

    /**
     * Get Total Time Of Orders LastDropScan in this Route .
     */
    public function LastDropScan()
    {
        // gating current routs tasks ids
        $tasks_ids = $this->GetAllTaskIds();
        $data = SprintTaskHistory::whereIn('sprint__tasks_id',$tasks_ids)->where('status_id',17)->orderBy('created_at','desc')->Active()->first()->toArray();
        return $data['created_at'];

    }

    /**
     * Get Total Time Of Orders LastDropScan in this Route .
     */
    public function FirstSortScan()
    {
        // gating current routs tasks ids
        $tasks_ids = $this->GetAllTaskIds();
        $data = SprintTaskHistory::whereIn('sprint__tasks_id',$tasks_ids)->where('status_id',133)->orderBy('created_at','asc')->Active()->first()->toArray();
        return $data['created_at'];
    }


}