
<a href="{{backend_url('sub/admin/profile/'.base64_encode($record->id))}}" title="Detail" class="btn btn-primary btn-xs file" style="float: left;">
    <i class="fa fa-folder">

    </i>
</a>


<a href="{{ backend_url('subadmin/edit/'.base64_encode($record->id)) }}" class="btn btn-info btn-xs edit" style="float: left;"><i class="fa fa-pencil">

    </i>  </a>


{!! Form::model($record, ['method' => 'delete', 'url' => 'sub/admin/'.$record->id, 'class' =>'form-inline form-delete']) !!}
{!! Form::hidden('id', $record->id) !!}
{!! Form::button('<i class="fa fa-trash-o"></i>  ', ['class' => 'btn btn-danger btn-xs', 'name' => 'delete_modal','data-toggle' => 'modal']) !!}
{!! Form::close() !!}
