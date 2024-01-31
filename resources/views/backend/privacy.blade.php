@extends( 'backend.layouts.app' )

@section('title', 'Dashboard')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <!-- Image Viewer CSS -->
    <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">
    <!-- Custom Light Box Css -->
    <link href="{{ backend_asset('css/custom_lightbox.css') }}" rel="stylesheet">
    <link href="{{ backend_asset('css/icofont.min.css')}}" rel="stylesheet"/>
    <link href="{{ backend_asset('css/dashboard.css')}}" rel="stylesheet"/>
    <link href="{{ backend_asset('css/owl.carousel.min.css')}}" rel="stylesheet"/>
    <style>
        /*pie chart box css*/
        .row.charts-box-main-wrap {
            position: relative;
        }
        .charts-box-ajax-data-loader-wrap {
            position: absolute;
            top: 0px;
            left: 0px;
            z-index: 1;
            width: 100%;
            height: 100%;
            text-align: center;
            padding: 0px 10px;
            display: none;
        }

        .charts-box-ajax-data-loader-wra.show
        {
            display: block;
        }
        .charts-box-ajax-data-loader-inner-wrap {
            position: relative;
            background-color: #0000006e;
            height: 100%;
        }
        .charts-box-ajax-data-loader-inner-wrap .lds-facebook {
            top: 47%;
        }

        .charts-box-ajax-data-loader-inner-wrap p
        {
            position: relative;
            top: 45%;
            color: #fff;
        }
        .dashboard-statistics-box {
            min-height: 600px;
            margin: 15px 0px;
            padding: 20px 0px;
            position: relative;
            box-sizing: border-box;
        }

        .lds-facebook {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 80px;
        }
        .lds-facebook div {
            display: inline-block;
            position: absolute;
            left: 8px;
            width: 16px;
            background: #fff;
            animation: lds-facebook 1.2s cubic-bezier(0, 0.5, 0.5, 1) infinite;
        }
        .lds-facebook div:nth-child(1) {
            left: 8px;
            animation-delay: -0.24s;
        }
        .lds-facebook div:nth-child(2) {
            left: 32px;
            animation-delay: -0.12s;
        }
        .lds-facebook div:nth-child(3) {
            left: 56px;
            animation-delay: 0;
        }
        .container::before, .container::after {
            content: "" !important;
            display: none !important;
        }
        .heading_area {
            padding: 30px 0;
            margin-bottom: 40px;
        }

        @keyframes lds-facebook {
            0% {
                top: 8px;
                height: 64px;
            }
            50%, 100% {
                top: 24px;
                height: 32px;
            }
        }
    </style>
@endsection

@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>
    <script src="{{ backend_asset('libraries/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/Chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/owl.carousel.min.js')}}"></script>
    <!-- Custom Theme JavaScript -->
    <script src="{{ backend_asset('js/sweetalert2.all.min.js') }}"></script>
    <!-- Custom Light Box JS -->
    <script src="{{ backend_asset('js/custom_lightbox.js')}}"></script>
    <script src="{{ backend_asset('js/echarts.min.js') }}"></script>

@endsection
@section('content')
    <div class="right_col" role="main" style="min-height: 895px;">
        <div class="inner-page">
        <div id="main_banner" class="style2 withBottomBg">

            <div class="heading_area">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12">
                            <h1 class="main_heading">JoeyCo Privacy Policy</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <section class="section">
            <div class="container">
                <article class="article">

                    <h2 class="basecolor3">Private Information Protection</h2>
                    <p>In order to complete your order successfully, JoeyCo™ requires your personal information. Under no circumstances will any of your personal data or personal identity be shared and will be used solely within JoeyCo. This information could possibly be used for statistical analysis and to create reports that will help us serve you better and could potentially be shared with affiliated parties, however your personal identity will not be compromised. In the unfortunate event of any legal complications, JoeyCo will be obliged to share your personal information with legal authorities. JoeyCo understands that your information shared with us is sensitive and thus acts in accordance with the Personal Information Protection and Electronic Documents Act Canada.</p>

                    <h2 class="basecolor3">Geolocation Terms</h2>
                    <p>As the user, you have the option of either providing your postal code or clicking on the geolocator icon. All data available to use through the geolocator will be used internally for statistical analysis and to produce reports that will help us serve you better. In no way will any of your personal information be shared or your personal identity be compromised through this system.</p>

                    <h2 class="basecolor3">Payment Process</h2>
                    <p>To process your payment, your credit card information is required. Through our secure servers, your credit card information will remain confidential. Once your order has been confirmed and processed, there are no refunds. A five minute grace period is made available for you to make any additions or modifications to your order. This grace period, however , is at the discretion of the merchant if your order has already been processed by them. After your payment has been processed, it can or may take up to 15 days for the amount to be credited.</p>

                    <h2 class="basecolor3">Order Process</h2>
                    <p>There is open accessibility to our merchant database on our website, which you can browse through and then create your order. At the point of confirming your order, you will be required to either sign in or create a new user account, which requires some of your personal information such as your name, telephone number, email address and physical address. It is your responsibility to ensure that the information on your user account is correct and kept updated. We understand that this information shared with us is confidential and will be treated as such through our secure servers and our adherence to the information protection laws in Canada. This personal information provides us with your location that we then use to ensure a swift delivery of your order.</p>
                    <p>The ordering process is made up of three steps, where the order must first be confirmed by you, the user, second by the merchant and third by the Joey. Once the order has been confirmed by you, the merchant and the Joey will be notified of the order. Once they have accepted and confirmed your order, cancellation will not be permitted. Once you have confirmed your order but do not receive a confirmation email within 5 minutes, please call 1-855-5-JOEYCO.</p>
                    <p>JoeyCo retains the right of refusal to any job under any circumstances that we deem fit such as where information provided to us is incorrect or mismatched and so on. Furthermore, at the time of delivery, if the Joey is unable to contact you in any manner, your order will be cancelled after a 10 minute window, but you will still be subjected to any charges affiliated with that order. JoeyCo cannot guarantee the completion of any order as the third parties involved retain their right of refusal to any job. In the event of an order cancellation or refusal, you will be notified either via email and/or by phone.</p>
                    <p>JoeyCo aims in fulfilling all orders within a sixty minute window, however, in the event that the delivery cannot be completed within the estimated time given due to unforeseen circumstances, JoeyCo is not held accountable.</p>

                    <h2 class="basecolor3">Merchant &#38; Charges</h2>
                    <p>JoeyCo is not the same as, nor is it related to, any of the merchants or businesses appearing or linked to on this website, except those appearing expressly under the JoeyCo name.</p>
                    <p>JoeyCo rates are determined by two factors; the nature of the delivery and the distance between the location of delivery and the merchant.These rates do not include gratuity. JoeyCo service charges are subject to change at any time without prior notification. The email address that you provide to us on your user account will be used as a point of contact to email you receipts and other information regarding your orders.</p>

                    <h2 class="basecolor3">Return policy and Dietary Related Issues</h2>
                    <p>Return policies are dependent on the policies set by the merchant. The receipt provided by JoeyCo can be used for any returns where applicable. JoeyCo is not liable for any returns or exchanges.</p>
                    <p>In any circumstance where you are not satisfied with the good or service you have ordered through JoeyCo, JoeyCo can help you by connecting you with the merchant but JoeyCo is not liable.</p>
                    <p>In the case for food orders and issues pertaining to dietary restrictions or allergies should be explicitly noted under the ‘additional note’ section. Users are advised to use and be aware of labelling in the menus provided. If any dietary related situation were to crop up, JoeyCo is not held responsible.</p>

                    <h2 class="basecolor3">Services &#38; Changes to privacy policy</h2>
                    <p>JoeyCo services are available 24 hours, 7 days a week. However, if the website is unavailable due to any unforeseen circumstances, JoeyCo will not be held liable.</p>
                    <p>JoeyCo reserves the right to make any changes to our privacy policy without any notifications and it is up to you, the user, to ensure that you are in constant familiarity with our privacy policy.</p>
                    <p>For any queries regarding our privacy policy, please do not hesitate to email us at <a href="mailto:info@joeyco.com">info@joeyco.com</a></p>
                </article>
            </div>

        </section>
    </div>
    </div>
@endsection

