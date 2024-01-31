$(function () {

    // SELECTORs
    var SELECTOR = {
            header: '#header',
            form: 'form',
            formControl: '.form-control',
            submitButton: '.submitButton',
            customUploadControl: '.custom-upload-control',
            menuHamburgerIcon: '.menu-hamburger-icon',
            menuCloseBtn: '.menu-close-btn',
            widgetTitle: '.widgetTitle',
            mainNav: '#main-nav',
            mainNavLink: '#main-nav a',
            subMenu: '.sub-menu',
            inputExpiryDate: '.input-expiry-date',
            inputSIN: '[name="sin"]',
            sinExpiryDate: '#sinExpiryDate',
            inputFile: 'input[type="file"]',
            inputCardNumber: '#card-number',
            ccExpirationDate: '#ccExpirationDate',
            documentForm: '#document-form',
            hearAboutUs: '.hearAboutUs',
            ssrValidation: '.ssr-validation',
            clientHeader: '#client-header',
            pageTrainingQuiz: '.page-training-quiz',
            quizQuestionList: '.quiz_question_list',
            quizQuestionBox: '.quiz_question_list .quiz_question',
            trainingVideo: '.training_video',
            videoPlayBtn: '#videoPlayBtn',
            videoSwitchBtn: '.videoSwitchBtn',
            videoFrame: '.video_frame',
            trainingItemsList: '.training_items_list',
            hasHSTnumber: '#hasHSTnumber',
            HSTnumberCheck: '.HSTnumberCheck',
            csDatepicker: '.cs-datepicker',
            startDate: '[name="startDate"]',
            endDate: '[name="endDate"]',
        },
        SIN_TYPE = {
            TEMPORARY: 'temporary',
            PERMANENT: 'permanent'
        },
        w_width = $( window ).width();

    // --------------------- Common - [Start]
    var COMMON = {
        validateSingleFile: function(thisFile){
            var thisUploadControl = thisFile.closest('.custom-upload-control');
            if(!thisFile.val() && thisFile.attr('required')){
                thisUploadControl.addClass('invalid');
            } else {
                thisUploadControl.removeClass('invalid');
            }
        },
        validateFiles: function() {
            $('input[type="file"]').each(function(){
                COMMON.validateSingleFile($(this));
            })
        },
        validateForm: function(thisElement, e){
            var form = thisElement.closest('form');
            if (form[0].checkValidity() === false) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.addClass('was-validated');
            $('.form-control').each(function(){
                if(!$(this).attr('required')){
                    $(this).addClass('no_valid_style');
                }
            })
            COMMON.validateFiles();
        },

        clientHamburger: function(){
            $(SELECTOR.clientHeader + ' .hamburger').on('click', function(e){
                e.preventDefault();
                $('#client-nav').addClass('open');
            });
            $(SELECTOR.clientHeader + ' .close-btn').on('click', function(e){
                e.preventDefault();
                $('#client-nav').removeClass('open');
            });
        },

        // Handling hamburger menu
        closeMainNav: function(){
            $(SELECTOR.header).removeClass('menuOpened');
        },
        openMainNav: function(){
            $(SELECTOR.menuHamburgerIcon).on('click', function(){
                if($(SELECTOR.header).hasClass('menuOpened')){
                    COMMON.closeMainNav();
                } else {
                    $(SELECTOR.header).addClass('menuOpened');
                }
            })
        },
        // Handling submit button for all forms
        updateSubmitButton: function(){
            $(SELECTOR.form).each(function(index){
                var thisForm = $(this),
                    isValid = thisForm[0].checkValidity();
                if(isValid){
                    thisForm.find('[type="submit"]').removeAttr('disabled');
                } else {
                    thisForm.find('[type="submit"]').attr('disabled');
                }
            })
        },
        readURL: function(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#blah').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        },

        updateActiveNav: function(){
            var page = $('#main').data('page');
            $('a[href="' + page + '.php"]').closest('li').addClass('active');
        },
        sidebarWidget: function(){
            $(SELECTOR.widgetTitle).on('click', function(){
                $(this).closest('.widget_sidebar').find('.widgetInfo').slideToggle('fast');
            })
        },
        responsiveTable: function(){
            if($('.tbl-responsive').length > 0){
                $('.tbl-responsive thead th').each(function(index, element){
                    var getTHIndex = $(this).index();
                    var dataTH = $(this).html();

                    $(this).closest('.tbl-responsive').find('tbody td').each(function(index, element){
                        var getTDIndex = $(this).index();

                        if(getTDIndex == getTHIndex){
                            $(this).prepend('<span class="mb-label d-block d-sm-none">'+dataTH+'</span>');
                        }
                    });
                });
            }
        },
        mainMenuMobile: function(){
            $(SELECTOR.subMenu + ' li.active').closest(SELECTOR.subMenu).slideDown().closest('li').addClass('open-menu');
            $(SELECTOR.mainNavLink).on('click', function(e){
                var thisSubmenu = $(this).closest('li').find(SELECTOR.subMenu);
                if(thisSubmenu.length){
                    e.preventDefault();
                    $(this).closest('li').toggleClass('open-menu');
                    thisSubmenu.slideToggle();
                }
            })
        },
        expiryDate: function() {
            if($(SELECTOR.inputExpiryDate).length){
                $(SELECTOR.inputExpiryDate).datepicker({
                    autoclose: true,
                    startDate: new Date(),
                    startView: 2,
                    format: 'yyyy-mm-dd',
                });
            }
        },
        csDatepicker: function() {
            if($(SELECTOR.csDatepicker).length){
                $(SELECTOR.csDatepicker).datepicker({
                    autoclose: true,
                    startDate: new Date(),
                    startView: 2,
                    format: 'yyyy-mm-dd',
                });
            }
        },
        startDatePicker: function() {
            if($(SELECTOR.startDate).length){
                $(SELECTOR.startDate).datepicker({
                    autoclose: true,
                    endDate: new Date(),
                    startView: 2,
                    format: 'yyyy-mm-dd',
                }).on('changeDate', function(data){
                    var selectedDate = data.date;
                    year = selectedDate.getFullYear(),
                        month = selectedDate.getMonth()+1,
                        date = selectedDate.getDate(),
                        endDate = year + '-' + month + '-' + date;
                    $(SELECTOR.endDate).datepicker('setStartDate', new Date(endDate));
                });
            }
        },
        endDatePicker: function() {
            if($(SELECTOR.endDate).length){
                $(SELECTOR.endDate).datepicker({
                    autoclose: true,
                    startDate: new Date('2021-02-02'),
                    endDate: new Date(),
                    format: 'yyyy-mm-dd',
                });
            }
        },
        sinexpiryDateInit: function() {
            if($(SELECTOR.sinExpiryDate).length){
                $(SELECTOR.sinExpiryDate).datepicker({
                    autoclose: true,
                    startDate: new Date(),
                    startView: 2,
                    format: 'yyyy-mm-dd',
                });
            }
        },
        ccExpiryDate: function() {
            if($(SELECTOR.ccExpirationDate).length){
                $(SELECTOR.ccExpirationDate).datepicker({
                    autoclose: true,
                    startDate: new Date(),
                    format: "mm/yyyy",
                    changeMonth: true,
                    changeYear: true,
                    changeDate: false,
                    startView: 2,
                    viewMode: "months",
                    minViewMode: "months"
                }).on('changeDate', function(e) {
                    var selectedDate = e.date;
                    var formatedSelectedDate = moment(selectedDate).format('YYYY-MM-DD');
                    $('#ccExpiryDate').val(formatedSelectedDate);
                });
            }
        },
        sinExpiryCheckHandler: function() {
            var selectedSINVal = $(SELECTOR.inputSIN + ':checked').val();
            if(selectedSINVal === SIN_TYPE.PERMANENT){
                $(SELECTOR.sinExpiryDate).closest('.form-group').addClass('d-none');
                $(SELECTOR.sinExpiryDate).removeAttr('required');
            } else {
                $(SELECTOR.sinExpiryDate).closest('.form-group').removeClass('d-none');
                $(SELECTOR.sinExpiryDate).attr('required', 'required');
            }
        },
        markHearAboutUsRequired: function(selectedVal){
            var selectedVal = $(SELECTOR.hearAboutUs).val();
            if(selectedVal === 'other'){
                $('input#other').attr('value', '');
                $('.other_wrap').removeClass('hide').attr('required', 'required');
                $('input#other').removeClass('hide').attr('required', 'required');
            } else {
                $('input#other').attr('value', selectedVal);
                $('input#other').addClass('hide').removeAttr('required');
                $('.other_wrap').addClass('hide').removeAttr('required');
            }
        },
        initHearAboutUs: function() {
            $(SELECTOR.hearAboutUs).on('change', function() {
                var selectedVal = $(this).val();
                COMMON.markHearAboutUsRequired(selectedVal);
            })
            // $(SELECTOR.hearAboutUs + ' input[type="checkbox"]').on('change', function() {
            //     // COMMON.markHearAboutUsRequired();
            // })
        },
        markInvalidFromServer: function() {
            var invalidFormControl = $(SELECTOR.ssrValidation + ' .invalid-feedback').closest('.form-group').find('.form-control');
            invalidFormControl.addClass('is-invalid');
            invalidFormControl.on('keyup', function() {
                invalidFormControl.removeClass('is-invalid');
            })
        },
        customSelectDropdown: function(){
            $('select').wrap('<div class="custom_dropdown"></div>');
        },
        // On page load
        init: function(){
            // Default initialization - [Start]
            COMMON.openMainNav();
            COMMON.updateActiveNav();
            COMMON.mainMenuMobile();
            COMMON.customSelectDropdown();

            COMMON.updateSubmitButton();
            if(w_width < 768){
                COMMON.sidebarWidget();
            }

            COMMON.responsiveTable();
            COMMON.expiryDate();
            COMMON.ccExpiryDate();
            COMMON.sinExpiryCheckHandler();
            COMMON.sinexpiryDateInit();
            COMMON.csDatepicker();
            COMMON.startDatePicker();
            COMMON.endDatePicker();

            COMMON.initHearAboutUs();
            COMMON.markInvalidFromServer();
            COMMON.clientHamburger();
            // Default initialization - [/end]

            if($(SELECTOR.inputCardNumber).length){
                $(SELECTOR.inputCardNumber).numbermask({
                    mask:"####-####-####-####"
                });
            }

            $(SELECTOR.menuCloseBtn).on('click', function(){
                COMMON.closeMainNav();
            });

            // Validate Form on submit click
            $(SELECTOR.submitButton).on('click', function(e){
                COMMON.validateForm($(this), e);
            });

            $(SELECTOR.formControl).on('keyup change', function(thisElement){
                COMMON.updateSubmitButton($(this));
            })

            $(SELECTOR.inputFile).on('change', function() {

            })

            $('.upload-file-link').on('click', function(e){
                e.preventDefault()
                $(this).closest('.upload-box').find('[type="file"]').click();
            });
            $('[type="file"]').on('change', function(){
                var file = $(this).prop('files')[0];
                var fileVal = $(this).val();
                var ext = fileVal.split('.').pop();
                if (file) {
                    var imagen = '';
                    $(this).closest('.upload-box').find('.uploaded-image-inner [class*="icofont-"]').hide();
                    if(ext === 'pdf'){
                        imagen = '';
                        $(this).closest('.upload-box').find('.uploaded-file').addClass('dp-none');
                        $(this).closest('.upload-box').find('.uploaded-image-inner').addClass('pdf-file').removeClass('doc-file');
                    } else if(ext === 'docx') {
                        $(this).closest('.upload-box').find('.uploaded-file').addClass('dp-none');
                        $(this).closest('.upload-box').find('.uploaded-image-inner').addClass('doc-file').removeClass('pdf-file');
                    } else {
                        imagen = URL.createObjectURL(file);
                        $(this).closest('.upload-box').find('.uploaded-image-inner').removeClass('pdf-file doc-file');
                        $(this).closest('.upload-box').find('.uploaded-file').removeClass('dp-none');
                    }
                    $(this).closest('.upload-box').find('.uploaded-file').attr('src', imagen);
                    $(this).closest('.upload-box').find('.profile-photo .center_lbl').addClass('d-none');
                }
                COMMON.validateSingleFile($(this));
            });

            // $(SELECTOR.customUploadControl + ' .profile-photo').on('click', function(){
            //     $(this).closest('.upload-box').find('[type="file"]').click();
            // })

            $('#attachfile').on('click', function () {
                $("#theFileInput").trigger('click'); // or triggerHandler or click()
            });

            $(SELECTOR.inputSIN).on('change', function(){
                COMMON.sinExpiryCheckHandler();
            })

            if($('.section-signup.section').length){
                $(".signup-now").on('click', function(e) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: $(".section-signup").offset().top
                    }, 500);
                });
            }

            $(".play-video").bind().click(function(){
                var src = $(".modal-video").attr('data-src'),
                    newSrc = src + '&autoplay=1&mute=0';
                $(".modal-video").attr('src', newSrc);
            });

            $('#demoJoeyApp').on('hidden.bs.modal', function (e) {
                $(".modal-video").attr('src', '');
            })
        }
    }
    // --------------------- Page Login - [Start]
    var PAGE_LOGIN = {
        init: function(){

        }
    }
    // --------------------- Page Login - [/end]


    // --------------------- Page Quiz - [Start]
    var PAGE_QUIZ = {
        updateProcessQuiz: function(){
            var totalQuestions = $(SELECTOR.quizQuestionBox).length,
                totalDoneQuestions = $(SELECTOR.quizQuestionBox + '.done').length,
                totalPercentage = parseInt(totalDoneQuestions) / parseInt(totalQuestions)*100;
            $(SELECTOR.pageTrainingQuiz + ' .quiz_progress .progress_status_bar').attr('data-test', 'test').css({"width": `${totalPercentage}%`});
            $(SELECTOR.pageTrainingQuiz + ' .question_numbers span').text(`${totalDoneQuestions} / ${totalQuestions}`);
        },
        validateQuiz: function(){
            $(SELECTOR.quizQuestionBox).each(function(){
                if($(this).find('[type="radio"]').is(':checked')){
                    $(this).find('.next-quiz-btn').removeClass('disabled');
                } else {
                    $(this).find('.next-quiz-btn').addClass('disabled');
                }
            })
        },
        checkQuiz: function(){

        },
        chooseAnswer: function(){
            $(SELECTOR.quizQuestionBox).on('change', function(){
                PAGE_QUIZ.validateQuiz();
            });
        },
        moveNextQuestion: function(){
            $(SELECTOR.quizQuestionBox + ' .next-quiz-btn').on('click', function(e){
                if(!$(this).hasClass('last')){
                    e.preventDefault();
                }
                if(!$(this).hasClass('disabled')){
                    $(this).closest('.quiz_question').addClass('done hide').next('.quiz_question').removeClass('hide').addClass('active');
                    PAGE_QUIZ.updateProcessQuiz();
                }
            })
        },
        init: function(){
            $(SELECTOR.quizQuestionBox + ':first-child').removeClass('hide').addClass('active');
            $(SELECTOR.quizQuestionBox + ':last-child .next-quiz-btn').text('Done').addClass('last');
            PAGE_QUIZ.validateQuiz();
            PAGE_QUIZ.checkQuiz();
            PAGE_QUIZ.chooseAnswer();
            PAGE_QUIZ.moveNextQuestion();
        }
    }
    // --------------------- Page Quiz - [/end]

    // --------------------- Page Trainings - [Start]
    var PAGE_TRAINING = {
        switchViewHandler: function(){
            $(SELECTOR.videoSwitchBtn).on('click', function(e){
                e.preventDefault();
                var dataURL = $(this).data('url'),
                    video = $(SELECTOR.videoFrame + ' video'),
                    itemRows = $(SELECTOR.trainingItemsList).find('.item_row'),
                    thisItemRow = $(this).closest('.item_row');

                video.find('source').attr('src', dataURL);
                video[0].load();
                $(SELECTOR.videoPlayBtn).trigger('click');

                itemRows.find('.item_actions .btn').removeClass('btn-border');
                itemRows.find('.item_actions i').removeClass('icofont-refresh').addClass('icofont-ui-play');
                itemRows.removeClass('active');
                itemRows.find('.item_actions .btn span').text('Play');
                thisItemRow.addClass('active');
                thisItemRow.find('.item_actions .btn').addClass('btn-border');
                thisItemRow.find('.item_actions i').removeClass('icofont-ui-play').addClass('icofont-refresh');
                thisItemRow.find('.item_actions .btn span').text('Watching');
                PAGE_TRAINING.videoPlay();
            })
        },
        downloadDocument: function(){
            // for non-IE
            $('.doc_btn_wrap .btn').on('click', function (e) {
                e.preventDefault();
                var fileURL = $(this).attr('href'),
                    fileExt = fileURL.split(/[#?]/)[0].split('.').pop().trim(),
                    fileName = fileURL.substring(this.href.lastIndexOf('/') + 1);
                $.ajax({
                    // url: fileURL,
                    method: 'GET',
                    url: "http://localhost/onboarding/public/download-file",
                    data:{fileData:fileURL},
                    /*xhrFields: {
                        responseType: 'blob'
                    },*/
                    success: function (data) {
                        var a = document.createElement('a');
                        var url = window.URL.createObjectURL(data);
                        console.log('url: ', url);
                        a.href = url;
                        a.download = fileName;
                        document.body.append(a);
                        a.click();
                        a.remove();
                        window.URL.revokeObjectURL(url);
                    }
                });
            });
        },
        scrollToVideo: function(){
            $('html, body').animate({
                scrollTop: $(SELECTOR.trainingVideo).offset().top
            }, 500);
        },
        videoPause: function(){
            var video = $(SELECTOR.videoFrame).find('video');
            $(SELECTOR.videoPlayBtn).removeClass('played');
            video.get(0).pause();
        },
        videoPlay: function(){
            $(SELECTOR.videoPlayBtn).on('click', function(){
                $(this).addClass('played');
                var video = $(this).closest('.video_frame').find('video');
                if($(this).hasClass('played')){
                    video.get(0).play();
                }
                PAGE_TRAINING.scrollToVideo();
            })
        },
        init: function(){
            var video = $(SELECTOR.videoFrame).find('video');
            video.on('click', function(e){
                e.preventDefault();
                PAGE_TRAINING.videoPause();
            })
            PAGE_TRAINING.videoPlay();
            PAGE_TRAINING.switchViewHandler();
            PAGE_TRAINING.downloadDocument();
        },
    }
    // --------------------- Page Trainings - [/end]

    // --------------------- Page Signup - [Start]
    var PAGE_SIGNUP_STEPS = {
        checkHSTnumberCheckbox: function(){
            // HST number for additional information
            if($(SELECTOR.hasHSTnumber).is(':checked')){
                $(SELECTOR.HSTnumberCheck).show();
                $(SELECTOR.HSTnumberCheck + ' input').attr('required', 'required');
            } else {
                $(SELECTOR.HSTnumberCheck).hide();
                $(SELECTOR.HSTnumberCheck + ' input').removeAttr('required');
            }
        },
        init: function() {
            PAGE_SIGNUP_STEPS.checkHSTnumberCheckbox();

            $(SELECTOR.hasHSTnumber).on('change', function(){
                PAGE_SIGNUP_STEPS.checkHSTnumberCheckbox()
            })
        },
    }
    // --------------------- Page Signup - [/end]

    // --------------------- Page Document - [Start]
    var PAGE_DOCUMENT = {
        init: function(){
            $('[data-target="#documentViewModal"]').on('click', function(){
                var img = $(this).find('img').attr('data-src'),
                    title = $(this).closest('.section-content').find('.doc-heading').find('h4').text();
                $('#documentViewModal').find('img').attr('src', img);
                $('#documentViewModal .modal-title').text(title);
            })
        }
    }
    // --------------------- Page Document - [/end]

    var PAGE_PAYMENT = {
        init: function() {
            $('.add-payment-method').on('click', function(e) {
                e.preventDefault();
                $('.creditcard-form-section').slideToggle();
            })
        }
    }

    COMMON.init()
    PAGE_LOGIN.init();
    PAGE_QUIZ.init();
    PAGE_TRAINING.init();
    PAGE_SIGNUP_STEPS.init();
    PAGE_DOCUMENT.init();
    PAGE_PAYMENT.init()
});