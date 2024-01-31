<?php
$user = Auth::user();
if ($user->email != "admin@gmail.com") {

    $data = explode(',', $user['rights']);
    $permissions = explode(',', $user['permissions']);
} else {
    $data = [];
    $permissions = [];
}
?>
<style>
.tab-content>.active{
    opacity: 1;
}

.section-content {
    border-bottom: solid 1px #f0eae5;
    padding: 36px 0;
    float: left;
    width: 100%;
}
.custom-upload-control:not(.profile-photo-wrap) .upload-box {
    border: dashed 1px rgba(180, 164, 155, 0.7);
    padding: 15px 15px;
    position: relative;
    -webkit-border-radius: 10px;
    -khtml-border-radius: 10px;
    -moz-border-radius: 10px;
    -ms-border-radius: 10px;
    -o-border-radius: 10px;
    border-radius: 10px;
}
.custom-upload-control .upload-box [type="file"] {
    /* display: none; */
    opacity: 0;
    height: 100%;
    width: 100%;
    position: absolute;
    z-index: 111;
}
.custom-upload-control .upload-box .uploaded-file-wrap:not(.profile-photo) {
    width: 150px;
    height: 100%;
    margin: 0 auto;
    position: absolute;
    top: 0;
    right: 0;
    z-index: 11;
    display: -webkit-box;
    display: -moz-box;
    display: box;
}
.custom-upload-control .upload-box .uploaded-file-wrap:not(.profile-photo) .uploaded-image-inner {
    position: relative;
    overflow: hidden;
    width: 120px;
    height: 90px;
    border: solid 1px #eae6e4;
    -webkit-border-radius: 10px;
    -khtml-border-radius: 10px;
    -moz-border-radius: 10px;
    -ms-border-radius: 10px;
    -o-border-radius: 10px;
    border-radius: 10px;
    margin-top: 10px;
}
body .h1, body .h2, body .h3, body .h4, body .h5, body .h6, body h1, body h2, body h3, body h4, body h5, body h6 {
    font-family: "Poppins", sans-serif !important;
    font-weight: 600;
    color: #e46d29 !important;
    margin-top: 0;
    margin-bottom: 14px;
    position: relative;
    line-height: 1.5em;
}
.custom-upload-control .upload-box .upload-box-inner p {
    font-size: 14px;
}
.custom-upload-control .upload-box .upload-box-inner .upload-icon {
    color: #e46d29;
    font-size: 36px;
    display: -webkit-box;
    display: -moz-box;
    display: box;
    display: -webkit-flex;
    display: -moz-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -moz-box-align: center;
    box-align: center;
    -webkit-align-items: center;
    -moz-align-items: center;
    -ms-align-items: center;
    -o-align-items: center;
    align-items: center;
    -ms-flex-align: center;
    -webkit-box-pack: center;
    -moz-box-pack: center;
    box-pack: center;
    -webkit-justify-content: center;
    -moz-justify-content: center;
    -ms-justify-content: center;
    -o-justify-content: center;
    justify-content: center;
    -ms-flex-pack: center;
    width: 50px;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
}
.link-wrap .link {
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10;
}
.custom-upload-control .upload-box .upload-box-inner {
    position: relative;
    padding-left: 65px;
    margin: 0 auto;
}
.status-wrap {
    font-size: 13px;
}
.pull-right {
    float: right !important;
}
.status-success {
    background: rgba(58, 185, 64, 0.2);
    color: #3ab940;
    padding: 2px 10px;
    display: inline-block;
}
.status-pending {
    background: rgba(255, 144, 0, 0.2);
    color: #ff9000;
    padding: 2px 10px;
    display: inline-block;
    border-radius: 100px;
}
.form-row {
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    margin-right: -5px;
    margin-left: -5px;
}
.form-group .form-check-inline, .form-group .custom-control-inline {
    margin: 0 0 15px;
}

.full-w {
    width: 100% !important;
}
.form-group:not(.has-radio):not(.has-checkbox):not(.no-min-h) {
    min-height: 102px;
    margin-bottom: 0;
}
.form-group label {
    color: #e46d29;
    display: block;
    margin-bottom: 0.3rem;
    font-weight: 600;
    font-size: 16px;
    line-height: 1.3em;
    text-transform: capitalize;
}
.row {
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
    justify-content: space-between;
}
.col-6 {
    max-width: 50%;
    width: 50%;
}
.custom-radio.form-radio.custom-control-inline input {
    display: none;
}
.col-6 {
    max-width: 50%;
    width: 50%;
    padding: 0 15px;
}
.content_footer_wrap {
    float: left;
    width: 100%;
}
#right_content {
    padding: 50px 0;
}
.divider-after:after {
    content: "";
    display: block;
    background: #e46d29;
    margin: 15px auto;
    width: 35px;
    height: 1px;
    position: absolute;
    bottom: 40px;
}
</style>
@extends( 'backend.layouts.app' )

@section('title', 'Dashboard')

@section('CSSLibraries')
    <style>
        .dashboard-statistics-box {
            min-height: 400px;
            margin: 15px 0px;
            position: relative;
            box-sizing: border-box;
        }

        .dashboard-statistics-box.dashboard-statistics-tbl-show td {
            padding-top: 52px;
            padding-bottom: 52px;
        }
    </style>
@endsection
@section('JSLibraries')
    <script src="{{ backend_asset('libraries/Chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ backend_asset('nprogress/nprogress.js') }}"></script>
    <script src="{{ backend_asset('libraries/gauge.js/dist/gauge.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/skycons/skycons.js') }}"></script>
    <script src="{{ backend_asset('libraries/Chart.js/dist/Chart.min.js') }}"></script>

@endsection


@section('content')
    <!--right_col open-->
    <div class="page-content-wrapper cutomMainbox_us" style="min-height:2100px !important">
        <aside id="right_content" class="col-12 col-lg-12">
            <div class="inner">
                <div class="content_header_wrap">
                    <div class="hgroup divider-after left">
                        <h1 class="lh-10">Documents</h1>
                        <h5 class="bf-color regular">Feel free to upload the following documents and submit them for
                            approval. <br/>We usually send approval within 24 hours.</h5>
                    </div>
                </div>

                @if (session()->has('message'))
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                        {{ session('message') }}
                    </div>
                @endif
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <form method="post" action="{{'document/save'}}" id="document-form" class="needs-validation"
                            novalidate enctype="multipart/form-data">
                            <!-- @csrf -->

                            <!-- Modal -->
                            <div class="modal fade" id="documentViewModal" tabindex="-1" aria-labelledby="documentViewModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="documentViewModalLabel">Modal title</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <img src="" alt="" class="full-w">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-xs btn-primary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @foreach($documentTypes as $doc)
                                <?php
                                $document_get = null;
                                $status_class = "status-pending";
                                $status_text = "Pending approval";
                                if (in_array($doc->id, $documents)) {
                                    $document_get = \App\JCDocument::where('jc_users_id', '=', auth()->user()->id)->where('document_type_id', '=', $doc->id)->first();
                                }
                                ?>
                                    @if($document_get)
                                        @if($document_get->is_approved == 1)
                                            <?php
                                            $status_class = "status-success";
                                            $status_text = "Approved";
                                            ?>
                                        @elseif($document_get->is_approved  == 0)
                                            <?php
                                            $status_class = "status-pending";
                                            $status_text = "Pending approval";
                                            ?>
                                        @else
                                            <?php
                                            $status_class = "status-error";
                                            $status_text = "Rejected";
                                            ?>
                                        @endif
                                    @else
                                        <?php
                                            $status_class = "status-pending";
                                            $status_text = "null";
                                        ?>
                                    @endif
                                @if($doc->document_type == 'file')

                                        <?php
                                        $driversPermitFilePath =null;
                                        $modal_data='';
                                        $doc_extensions = array("doc", "docx");
                                        $pdf_ex = "pdf";
                                        if ($document_get){
                                            $explode_name = explode('.', $document_get->document_data);
                                            $file_extention = end($explode_name);
                                            if (in_array($file_extention, $doc_extensions)) {
                                                $driversPermitFilePath = url('images/thumbnail.png');
                                            } elseif ($file_extention == $pdf_ex) {
                                                $driversPermitFilePath = url('images/pdf-thumbnail.jpg');
                                            } else {
                                                $modal_data = 'data-toggle="modal" data-target="#documentViewModal"';
                                                $driversPermitFilePath = $document_get->document_data;
                                            }
                                        }
                                        ?>
                                    <section class="section-content">
                                        <div class="section-inner">
                                            <div class="doc-heading"><h4>{{$doc->document_name}} </h4>
                                            </div>
                                            <div class="upload-row custom-upload-control row">
                                                <div class="col-sm-6 col-12">
                                                    <div class="upload-box">
                                                        <input type="hidden" name="documentIds[]" value="{{$doc->id}}">
                                                        <input type="file" name="document[{{$doc->id}}]"
                                                        accept="image/jpeg,image/gif,image/jpg,image/png,application/pdf"
                                                            onchange="checkFileExtension(this)"
                                                        <?php
                                                            $file_req = '';
                                                            if ($doc->is_optional == 0) {
                                                                if (!isset($document_get))
                                                                {
                                                                    $file_req = 'required';
                                                                }
                                                            }
                                                            echo $file_req; ?>
                                                    >
                                                        <div class="uploaded-file-wrap">
                                                            <div class="uploaded-image-inner">

                                                                @if($driversPermitFilePath)

                                                                    <a href="<?php echo isset($document_get) ? $document_get->document_data : "" ?>"
                                                                    target="_blank" <?php echo $modal_data?>>

                                                                        <img src="{{$driversPermitFilePath}}" data-src="<?php echo isset($document_get) ? $document_get->document_data : "" ?>" class="uploaded-file"/>
                                                                    </a>
                                                                    {{-- @elseif()
                                                                        @else
                                                                        @endif--}}
                                                                    @else
                                                                        <i class="icofont-not-allowed"></i>
                                                                        <img src="" class="uploaded-file dp-none" />
                                                                    @endif

                                                            </div>
                                                        </div>
                                                        <div class="upload-box-inner link-wrap">
                                                            <a href="#" class="link upload-file-link" javascript:void(0);></a>
                                                            <i class="upload-icon icofont-upload"></i>
                                                            <h5>Drop files or click to upload</h5>
                                                            <i class="upload-icon icofont-upload"></i>
                                                            <p>Maximum upload file size: 5 MB<br/> Accepted formats: png, jpeg,
                                                                jpg, pdf, doc & docx</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if ($doc->exp_date == 1)
                                                    <div class="col-sm-4 col-12">
                                                        <div class="expiry-date">
                                                            <div class="form-group no-min-h">
                                                                <label for="code">Expiry date</label>
                                                                <input type='text' class="form-control form-control-lg input-expiry-date datemask" placeholder="YYYY-MM-DD"
                                                                    name="expireDate[{{$doc->id}}]" <?php echo !empty($file_req) ? 'required' : '' ?>
                                                                value="<?php echo isset($document_get) ? $document_get->exp_date : "" ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="">
                                                    <div class="status-wrap pull-right"><span class="lbl">Status:</span> <span
                                                                class="{{$status_class}}">{{$status_text}}</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                @endif
                                @if($doc->document_type == 'text')
                                    <section class="section-content">
                                        <div class="section-inner">
                                            <div class="doc-heading"><h4>{{$doc->document_name}}  </h4>
                                                <div class="status-wrap pull-right"><span class="lbl">Status:</span> <span
                                                            class="{{$status_class}}">{{$status_text}}</span></div>
                                            </div>
                                            <div class="form-row">
                                                <input type="hidden" name="documentIds[]" value="{{$doc->id}}">
                                                <div class="form-group col-12 col-md-5">
                                                    <label for="color">Detail</label>
                                                    <input type='text' class="form-control form-control-lg"
                                                        placeholder="Enter Detail" name="documenttext[{{$doc->id}}]"
                                                        value="<?php echo isset($document_get) ? $document_get->document_data : "" ?>"
                                                        <?php echo $doc->is_optional == 0 ? 'required' : '' ?>
                                                        maxlength="<?php echo $doc->max_characters_limit ?>">
                                                </div>
                                                @if ($doc->exp_date == 1)
                                                    <div class="form-group col-12 col-md-4">
                                                        <label for="color">Expiry date</label>
                                                        <input type='text'
                                                            class="form-control form-control-lg input-expiry-date datemask" placeholder="YYYY-MM-DD"
                                                            name="expireDate[{{$doc->id}}]" <?php echo $doc->is_optional == 0 ? 'required' : '' ?>
                                                            value="<?php echo isset($document_get) ? $document_get->exp_date : "" ?>" >
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </section>
                                @endif
                                @if($doc->document_type == 'sin')
                                    <section class="section-content">
                                        <div class="section-inner">
                                            <div class="doc-heading"><h4>{{$doc->document_name}} </h4>
                                                <div class="status-wrap pull-right"><span class="lbl">Status:</span> <span
                                                            class="{{$status_class}}">{{$status_text}}</span></div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-12 col-md-6">
                                                    <label>Type</label>
                                                    <input type="hidden" name="documentIds[]" value="{{$doc->id}}">
                                                    <?php
                                                    $sinexp =0;
                                                    if ($document_get){
                                                        if ($document_get->exp_date !=null)
                                                            {
                                                                $sinexp = 1;
                                                            }
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="custom-radio form-radio custom-control-inline full-w">
                                                                <input class="form-radio-input sin-type" type="radio" name="sin"
                                                                    id="permanent" value="permanent" <?php echo $sinexp == 0 ? "checked" : "" ?> >
                                                                <label class="form-radio-label full-w" for="permanent">Permanent</label>
                                                            </div>
                                                        </div>

                                                        <div class="col-6">
                                                            <div class="custom-radio form-radio custom-control-inline full-w">
                                                                <input class="form-radio-input sin-type" type="radio" name="sin"
                                                                    id="temporary" value="temporary" <?php echo $sinexp == 1 ? "checked" : "" ?> >
                                                                <label class="form-radio-label full-w" for="temporary">Temporary</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-12 col-md-3">
                                                    <label for="color">SIN Number</label>
                                                    <?php $sinNumber = isset($document_get) ? $document_get->document_data : "";
                                                    $sinNumberDate =isset($document_get) ? $document_get->exp_date : "" ;?>
                                                    <input type="text" class="form-control form-control-lg"
                                                        placeholder="Sin Number" id="sinNumber"
                                                        name="documenttext[{{$doc->id}}]"
                                                        value="<?php echo isset($document_get) ? $document_get->document_data : "" ?>"
                                                        <?php echo $doc->is_optional == 0 ? 'required' : '' ?>

                                                        data-old-val="<?php echo isset($sinNumber) ? $sinNumber : ''; ?>"
                                                    data-old-type="<?php if (isset($sinNumberDate) == true && $sinNumberDate != "") {
                                                        echo 'temporary';
                                                    } else {
                                                        echo "permanent";
                                                    }?>"
                                                    >
                                                </div>
                                                @if ($doc->exp_date == 1)
                                                    <div class="form-group col-12 col-md-3" style="<?php if ($document_get) {
                                                        if ($document_get->exp_date) {
                                                            echo 'display: block !important';
                                                        }
                                                    } ?>">
                                                        <label for="license">Expiry date</label>
                                                        <!-- <input type="date" class="form-control form-control-lg" placeholder="12 May 2022" name="sinExpiryDate" id="sinExpiryDate"> -->
                                                        <input type="tel" name="expireDate[{{$doc->id}}]"
                                                            class="form-control form-control-lg datemask"  placeholder="YYYY-MM-DD"

                                                            id="sinExpiryDate" <?php echo $doc->is_optional == 0 ? 'required' : '' ?>
                                                            value="<?php echo isset($document_get) ? $document_get->exp_date : "" ?>">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </section>
                                @endif
                            @endforeach
                            <div class="content_footer_wrap">
                                <button type="submit" class="btn btn-primary submitButton"><?php if(count($documents)>0){echo "Update";}else{echo "Save";} ?></button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <section class="section-content">
                            <div class="section-inner">
                                <div class="box-style-1 align-center marginauto">
                                    <div class="doc-heading">
                                        <h4>Pending for approval</h4>
                                        <div class="status-wrap pull-right"><span class="lbl">Status:</span> <span
                                                    class="status-error">Approved</span></div>
                                    </div>
                                    <h6 class="regular">Your documents are currently under review.</h6>
                                    <p>Our support team will send you a confirmation email once your documents get approved
                                        or you may check the status of your documents above.</p>
                                    <p class="bc1-light">If you have any further queries, please don't hesitate to reach us
                                        at <a href="support@joeyco.com">support@joeyco.com</a></p>
                                    <div class="divider center"></div>
                                    <a href="{{url('contact-us')}}" class="btn btn-primary btn-border">Contact Support</a>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            </div>
        </aside>
    </div>
    <!-- /#page-wrapper -->
    <script>
        $(document).ready(function() {

            $('.datemask').mask('0000-00-00');
        });
    </script>
    <script>
        // sin_pattern
        var sin_pattern = /^[0123456789][0-9]{0,8}$/;
        let sin_old_type = $("#sinNumber").attr('data-old-type');

        if (sin_old_type == 'permanent') {
            sin_pattern = /^[0123456789][0-9]{0,8}$/;

        }
        else if (sin_old_type == 'temporary') {
            sin_pattern = /^[9][0-9]{0,8}$/;
        }

        $(function () {

            $('.sin-type').change(function () {

                $('.sin-no-error').remove();

                let el = $(this);
                var sin_type_value = $(this).val();

                // setting the old value on change if type is match
                let sin_old_type = $("#sinNumber").attr('data-old-type');
                let sin_old_value = $("#sinNumber").attr('data-old-val');
                if (sin_type_value == sin_old_type) {
                    $("#sinNumber").val(sin_old_value);
                }
                else {
                    $("#sinNumber").val(null);
                }


                if (sin_type_value == 'permanent') {
                    sin_pattern = /^[0123456789][0-9]{0,8}$/;


                }
                else if (sin_type_value == 'temporary') {
                    sin_pattern = /^[9][0-9]{0,8}$/;
                }

            });
        });


        //checking input value with match on keyup
        $(document).on('keyup', '#sinNumber', function (e) {

            let value = $(this).val();
            if (!$(this).val().match(sin_pattern)) {
                console.log(value);
                // checking the length
                if (value.length > 0) {
                    $(this).val(value.slice(0, -1));
                }
            }


        });

        //checking input value with match on keydown
        $(document).on('keydown', '#sinNumber', function (e) {

            let value = $(this).val();
            if (!$(this).val().match(sin_pattern)) {
                // checking the length
                if (value.length > 0) {
                    $(this).val(value.slice(0, -1));
                }
            }


        });

          //Check extention of upload files
          function checkFileExtension(element) {
              var el = $(element);
              var selectedText = el.val();
              if (selectedText) {
                  extension = selectedText.split('.').pop();
                  console.log(extension);
                  var allowed_ext = ['jpeg', 'png', 'jpg', 'doc', 'docx', 'pdf', 'PNG', 'JPEG', 'JPG', 'DOC', 'DOCX'];
                  if (!allowed_ext.includes(extension)) {

                      alert("Sorry! Invalid file. Allowed extensions are: jpeg, png, jpg, doc, docx & pdf. ");
                     // location.reload();
                      $(element).val('');
                  }
              }
          };

    </script>
@endsection
