@if(can_access_route('joey-performance-status.update',$userPermissoins))
    @if($record->is_approved == 0 && $record->unflaged_by == 0)
        <button class="btn btn-info btn-xs performance-status" title="Mark Approved" data-id="{{$record->id}}">
            Mark Approve
        </button>
    @elseif($record->unflaged_by > 0)
        <span class="label label-success">This Order Un-flag </span>
    @else
        <span class="label label-success">Approved  </span>
    @endif
@endif