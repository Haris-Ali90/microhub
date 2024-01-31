@if($record->image)
<a href="#" class="pop" onclick="previewImage(this)"><img src="{{$record->image}}" style="width: 80px;height: 80px;"class="uploaded-file"/></a>
    @endif
