
@if($record->status==0)

    @if(can_access_route('claims.statusUpdate-pending',$userPermissoins))
    <a href="#" class="btn btn-sm btn-info" onclick="openStatusChangeModal('{{$record->id}}')">Change Status</a>
    @endif

    @if(can_access_route('pendingClaimsSearchOrder.show',$userPermissoins))
    <a href="{{url('claims/search-orders/details/'.base64_encode($record->sprint_id))}}" class="btn btn-sm btn-primary" target="_blank">Order Details</a>
    @endif
    @if(can_access_route('pendingClaims.delete',$userPermissoins))
    <a href="{{url('claims/delete/'.$record->id)}}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this claim?');">Delete</a>
    @endif

@endif

@if($record->status==1)

    @if(can_access_route('claims.uploadImage-approved',$userPermissoins))
    <a href="#" class="btn btn-sm btn-info" onclick="openStatusChangeModal('{{$record->id}}')">Change Status</a>
    @endif
    @if(can_access_route('approvedClaimsSearchOrder.show',$userPermissoins))
    <a href="{{url('claims/search-orders/details/'.base64_encode($record->sprint_id))}}" class="btn btn-sm btn-primary" target="_blank">Order Details</a>
    @endif
    @if(can_access_route('approvedClaims.delete',$userPermissoins))
    <a href="{{url('claims/approved/delete/'.$record->id)}}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this claim?');">Delete</a>
    @endif
@endif

@if($record->status==2)

@if(can_access_route('claims.uploadImage-reject',$userPermissoins))
    <a href="#" class="btn btn-sm btn-info" onclick="openStatusChangeModal('{{$record->id}}')">Change Status</a>
    @endif
    @if(can_access_route('rejectClaimsSearchOrder.show',$userPermissoins))
    <a href="{{url('claims/search-orders/details/'.base64_encode($record->sprint_id))}}" class="btn btn-sm btn-primary" target="_blank">Order Details</a>
    @endif
    @if(can_access_route('rejectClaims.delete',$userPermissoins))
        <a href="{{url('claims/reject/delete/'.$record->id)}}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this claim?');">Delete</a>
    @endif
@endif

@if($record->status==3)

    @if(can_access_route('claims.statusUpdate-re-submitted',$userPermissoins))
    <a href="#" class="btn btn-sm btn-info" onclick="openStatusChangeModal('{{$record->id}}')">Change Status</a>
    @endif
    @if(can_access_route('re-submittedClaimsSearchOrder.show',$userPermissoins))
    <a href="{{url('claims/search-orders/details/'.base64_encode($record->sprint_id))}}" class="btn btn-sm btn-primary" target="_blank">Order Details</a>
    @endif
    @if(can_access_route('re-submittedClaims.delete',$userPermissoins))
        <a href="{{url('claims/re-submitted/delete/'.$record->id)}}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this claim?');">Delete</a>
    @endif

@endif


<!--@if(can_access_route('claims.statusUpdate',$userPermissoins))
<select onchange="changeStatus('{{$record->id}}',this,'claims/status-update')" class="form-control" name="statusId-{{$record->id}}" id="statusId" onFocus="saveValue(this);">
    <option disabled value="">Select an option</option>
        <option <?php if(0==$record->status){echo "selected";} ?> value="0">Pending</option>
        <option <?php if(1==$record->status){echo "selected";} ?> value="1">Approved</option>
        <option <?php if(2==$record->status){echo "selected";} ?> value="2">Not Approved</option>
        <option <?php if(3==$record->status){echo "selected";} ?> value="3">Re-Submitted</option>
</select>
@endif-->

