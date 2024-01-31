@if($record->status==0)
    @if(can_access_route('pendingClaims.delete',$userPermissoins))
        <input name='claims_id' type='checkbox' class='checkbox checkone' value='{{$record->id}}'>
    @endif

@endif


