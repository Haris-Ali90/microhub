{{--@if(can_access_route('sub-admin.show',$userPermissoins))--}}
<select class="form-control" name="reasonId" id="reasonId">
    <option value="">Select an option</option>
    @foreach ($reasonsList as $reason)
        <option <?php if($reason->id==$record->reason_id){echo "selected";} ?> value="{{$reason->id}}">{{$reason->title}}</option>
    @endforeach
</select>
{{--@endif--}}

