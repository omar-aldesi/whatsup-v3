@extends('admin.layouts.app')
@section('panel')
<section>
    <div class="card">
        <div class="card-header">
            <h6 class="card-title">{{translate('Send A Whatsapp Message')}}</h6>

            <div  data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Suggestions Note">
                <button class="i-btn info--btn btn--sm d-xl-none info-note-btn"><i class="las la-info-circle"></i></button>
            </div>
        </div>

        <div class="card-body position-relative">
            <form action="{{route('admin.whatsapp.store')}}" method="POST" enctype="multipart/form-data">
              @csrf
                <div class="row g-4">
                    <div class="col-xl-9 order-xl-1 order-2">
                        <div class="form-wrapper">
                            <h6 class="form-wrapper-title">{{translate('Choose audience')}}</h6>
                            <div class="file-tab">
                                <ul class="nav nav-tabs mb-3 gap-2" id="myTabContent" role="tablist">
                                    <li class="nav-item single-audience" role="presentation">
                                        <button class="nav-link active" id="single-tab" data-bs-toggle="tab" data-bs-target="#single-tab-pane" type="button" role="tab" aria-controls="single-tab-pane" aria-selected="true"><i class="las la-user"></i> {{ translate('Single Audience') }}</button>
                                    </li>
                                    <li class="nav-item group-audience" role="presentation">
                                        <button class="nav-link" id="group-tab" data-bs-toggle="tab" data-bs-target="#group-tab-pane" type="button" role="tab" aria-controls="group-tab-pane" aria-selected="false"><i class="las la-users"></i> {{ translate('Group Audience') }}</button>
                                    </li>
                                    <li class="nav-item import-file" role="presentation">
                                        <button class="nav-link" id="file-tab" data-bs-toggle="tab" data-bs-target="#file-tab-pane" type="button" role="tab" aria-controls="file-tab-pane" aria-selected="false"><i class="las la-file-import"></i> {{ translate('Import File') }}</button>
                                    </li>
                                </ul>

                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="single-tab-pane" role="tabpanel" aria-labelledby="single-tab" tabindex="0">

                                        <div class="form-item">
                                            <label class="form-label">{{ translate('Single Input') }}</label>
                                            <input type="number"  class="form-control" value="{{old("number")}}" name="number" id="number" placeholder="{{ translate('Enter with country code ')}}{{$general->country_code}}{{ translate('XXXXXXXXX')}}" aria-label="number" aria-describedby="basic-addon11">

                                            <div class="form-text">
                                                {{ translate('Put single or search from save contact')}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="group-tab-pane" role="tabpanel" aria-labelledby="group-tab" tabindex="0">
                                        <div class="form-item">
                                            <label class="form-label" for="group">{{ translate('From Group')}}</label>
                                            <select class="form-control keywords" name="group_id[]" id="group" multiple="multiple">
                                                <option value="" disabled="">{{ translate('Select One')}}</option>
                                                @foreach($groups as $group)
                                                    <option value="{{$group->id}}">{{$group->name}}</option>
                                                @endforeach
                                            </select>

                                            <div class="form-text">{{ translate('Can be select single or multiple group')}}</div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="file-tab-pane" role="tabpanel" aria-labelledby="file-tab" tabindex="0">
                                        <div class="form-item">
                                            <label class="form-label" for="file">{{ translate('Import File')}} <span id="contact_file_name"></span></label>

                                            <div class="upload-filed">
                                                <input type="file" name="file" id="file" />
                                                <label for="file">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <span class="upload-drop-file">
                                                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"x="0" y="0" viewBox="0 0 128 128" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path fill="#f6f0ff" d="M99.091 84.317a22.6 22.6 0 1 0-4.709-44.708 31.448 31.448 0 0 0-60.764 0 22.6 22.6 0 1 0-4.71 44.708z" opacity="1" data-original="#f6f0ff" class=""></path><circle cx="64" cy="84.317" r="27.403" fill="#6009f0" opacity="1" data-original="#6009f0" class=""></circle><g fill="#f6f0ff"><path d="M59.053 80.798v12.926h9.894V80.798h7.705L64 68.146 51.348 80.798zM68.947 102.238h-9.894a1.75 1.75 0 0 1 0-3.5h9.894a1.75 1.75 0 0 1 0 3.5z" fill="#f6f0ff" opacity="1" data-original="#f6f0ff" class=""></path></g></g></svg>
                                                        </span>
                                                        <span class="upload-browse">{{ translate("Upload File Here ") }}</span>
                                                    </div>
                                                </label>
                                            </div>

                                            <div class="form-text mt-3">
                                                {{ translate('Download Sample: ')}}
                                                <a href="{{route('demo.file.download', 'csv')}}" class="badge badge--primary"><i class="fa fa-download" aria-hidden="true"></i> {{ translate('csv')}}, </a>
                                                <a href="{{route('demo.file.download', 'xlsx')}}" class="badge badge--primary"><i class="fa fa-download" aria-hidden="true"></i> {{ translate('xlsx')}}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-wrapper">
                            <h6 class="form-wrapper-title">{{translate('Message and Schedule')}}</h6>

                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="form-item">
                                        <label class="form-label" for="message">{{ translate('Write Message')}} <sup class="text-danger">*</sup></label>
                                        <div class="my-2 d-flex flex-wrap align-items-center gap-2">
                                            <label for="media_upload" class="media_upload_label">
                                                <div id="uploadfile">
                                                    <input type="file" id="media_upload" hidden>
                                                </div>
                                                
                                                <div class="i-btn light--btn btn--sm">
                                                    {{ translate("Add Media") }}</span><i class="fa-solid fa-paperclip"></i>
                                                </div>
                                            </label>

                                            <a title="{{ translate("Bold") }}" href="#" class="style-link i-btn light--btn btn--sm " data-style="bold"><span class="fw-bold p-0 i-btn light--btn btn--sm me-2">{{ translate("Bold") }}</span><i class="fa-solid fa-bold"></i></a>
                                            <a title="{{ translate("Italic") }}" href="#" class="style-link i-btn light--btn btn--sm" data-style="italic"><span class="fst-italic p-0 i-btn light--btn btn--sm me-2">{{ translate("Italic") }}</span><i class="fa-solid fa-italic"></i></a>
                                            <a title="{{ translate("Mono Space") }}" href="#" class="style-link i-btn light--btn btn--sm" data-style="mono"><span class="font-monospace p-0 i-btn light--btn btn--sm me-2">{{ translate("Mono Space") }}</span><i class="fa-solid fa-arrows-left-right-to-line"></i></a>
                                            <a title="{{ translate("Strike") }}" href="#" class="style-link i-btn light--btn btn--sm" data-style="strike"><span class="text-decoration-line-through p-0 i-btn light--btn btn--sm me-2">{{ translate("Strike") }}</span><i class="fa-solid fa-strikethrough"></i></a>
                                            <a href="javascript:void(0)" class="i-btn info--btn btn--sm ms-auto" data-bs-toggle="modal" data-bs-target="#templatedata">{{ translate('Use Template')}} </a>
                                        </div>

                                        
                                        <div class="custom--editor">
                                            <div class="speech-to-text" id="messageBox">
                                                <textarea class="form-control message" name="message" id="message" placeholder="{{ translate('Enter SMS Content &  For Mention Name Use ')}}@php echo "{{". 'name' ."}}"  @endphp" aria-describedby="text-to-speech-icon">{{session()->get('old_sms_message')}}</textarea>
                                                <span class="voice-icon" id="text-to-speech-icon">
                                                    <i class='fa fa-microphone text-to-speech-toggle'></i>
                                                </span>
                                            </div>
                                            <div id="add_media" class="test"></div>
                                        </div>
                                        
                                        <div class="mt-4 d-flex align-items-center justify-content-md-between justify-content-start flex-wrap gap-3">
                                            <div class="text-end message--word-count"></div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-item">
                                        <label for="schedule_date" class="form-label">{{ translate('Schedule Date')}}</label>
					                    <input type="datetime-local" name="schedule_date" id="schedule_date" class="form-control schedule-date">
                                      
                                    </div>
                                </div>

                                <div class="col-md-6 schedule"></div>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="i-btn primary--btn btn--lg whatsapp-submit">
                                {{translate("Submit")}}
                            </button>
                        </div>
                    </div>

                    <div class="note-container col-xl-3 order-xl-2 order-1 d-xl-block d-none">
                        <div class="note">
                            <h6>{{translate('Suggestions Note')}}</h6>
                            <div class="note-body">
                                <p class="single-audience-note note-message">{{translate("By selecting the 'Single Audience' input field, you can enter a valid phone number with a country code (For Example: $general->country_code xxxxxxxxxx). In order to send or schedule an SMS, continue filling up the rest of the form. ")}}</p>
                                <p class="d-none group-audience-note note-message">{{translate("By selecting the 'Group Audience' input field, You can choose your personal Text Phonebook group to send or schedule messages to all of the group's contacts.")}}</p>
                                <p class="d-none import-file-note note-message">{{translate("By selecting the 'Import File' input field, You can upload your local .csv or .xlsv files from your machine and send or schedule messages to those contacts")}}</p>
                                <p class="d-none schedule-date-note note-message">{{translate("By selecting the 'Schedule Date' input field, You can pick date and type to send a message according to that schedule")}}</p>
                                <p class="d-none message-note note-message">{{translate("You can either type your message or click the 'mic' icon to use the text to speech feature. By using the ")}}@php echo "{{". 'name' ."}}"  @endphp {{ translate(" variable you can mention the name for that contact. But with 'Single Audience' selected only their number will pass by that variable.") }}</p>
                                <p class="d-none message-type-note note-message">{{translate("If you select the 'Text' option $general->sms_word_text_count characters will be allocated for a single SMS. And if you select 'unicode' then $general->sms_word_unicode_count characters will be allocated for each SMS.")}}</p>
                                <p class="d-none message-media-note note-message">{{translate("You can select from four different media types (Document, Image, Audio, Video) and attach them with your whatsapp messsage. Press the Choose File Button under 'Upload File' to upload your file.")}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

    <div class="modal fade" id="templatedata" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header bg--lite--violet">
                            <div class="card-title text-center text--light">{{ translate('SMS Template')}}</div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="template" class="form-label">{{ translate('Select Template')}} <sup class="text--danger">*</sup></label>
                                <select class="form-control" name="template" id="template" required>
                                    <option value="" disabled="" selected="">{{ translate('Select One')}}</option>
                                    @foreach($templates as $template)
                                        <option value="{{$template->message}}">{{$template->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script-push')
<script>
	(function($){
		"use strict";
		$('.keywords').select2({
			tags: true,
			tokenSeparators: [',']
		});

		$('.whatsapp-submit').on('click', function(){
				 
            if($('input[type=datetime-local][name=schedule_date]').val()){
                const html = `
                <input hidden type="number" value ="2" name="schedule" id="schedule" class="form-control">`;
                $('.schedule').append(html);
            }else{
                const html = `
                <input hidden type="number" value ="1" name="schedule" id="schedule" class="form-control">`;
                $('.schedule').append(html);
            }
        
            
        });

        //Contact File Input Details
        $("#file").change(function() {
            
            var contact_file = this.files[0];
            var file_name = "{{ translate('Selected: ') }}<p class='badge badge--primary'>"+ contact_file.name +"</p>";
            $("#contact_file_name").html(file_name);
        })

	    $('select[name=template]').on('change', function(){
	    	var character = $(this).val();
	    	$('textarea[name=message]').val(character);
		    $('#templatedata').modal('toggle');
		});

        var wordLength = {{$general->whatsapp_word_count}};

		$(`textarea[name=message]`).on('keyup', function(event) {
		 	var credit = wordLength;
            var character = $(this).val();
            var characterleft = credit - character.length;
            var word = character.split(" ");
            var sms = 1;
			if (character.length > wordLength) {
    			sms = Math.ceil(character.length / wordLength);
    		}
            if (character.length > 0) {
                $(".message--word-count").html(`
                	<span class="text--success character">${character.length}</span> {{ translate('Character')}} |
					<span class="text--success word">${word.length}</span> {{ translate('Words')}} |
					<span class="text--success word">${sms}</span> {{ translate('SMS')}} (${wordLength} Char./SMS)`);
            }else{
                $(".message--word-count").empty()
            }
        });

        var t = window.SpeechRecognition || window.webkitSpeechRecognition,
            e = document.querySelectorAll(".speech-to-text");
	    if (null != t && null != e) {
	        var n = new t;
            var e = !1;
        	$('#text-to-speech-icon').on('click',function () {
				var messageBox = document.getElementById('messageBox');
				messageBox.querySelector(".form-control").focus(), n.onspeechstart = function() {
                    e = !0
                }, !1 === e && n.start(), n.onerror = function() {
                    e = !1
                }, n.onresult = function(e) {
                    messageBox.querySelector(".form-control").value = e.results[0][0].transcript
                }, n.onspeechend = function() {
                    e = !1, n.stop()
                }
			});
	    }

        //File Update
        $(document).ready(function () {
            $(".media_upload_label").click(function () { 
                setDefaultFileInputAttributes();
            });
            $("#media_upload").change(function () {

                
                var file = this.files[0];
                var formattedDate = formatDate(file.lastModifiedDate);
                var fileDetailsHTML = '<div class="file__wrapper">' +
                    '<div class="file-detail">' +
                    '<a href="#" class="remove__file"><i class="fa-regular fa-circle-xmark"></i></a>' +
                    '<div>' +
                    getFileIcon(file.type) +
                    '</div>' +
                    '<span class="file-type d-none"><i class="fa-regular fa-file-pdf"></i></span>' +
                    '<div class="d-flex flex-column">' +
                    "<p title='"+ file.name +"' class='fw-normal'>" + "{{ translate('File Name: ') }}" + file.name + '</p>' +
                    "<p title='"+ file.type +"'>" + "{{ translate('File Type: ') }}" + file.type + '</p>' +
                    "<p title='"+ bytesToSize(file.size) +"'>" + "{{ translate('File Size: ') }}" + bytesToSize(file.size) + '</p>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
                
                $("#add_media").html(fileDetailsHTML);

                var fileType = getFileType(file.type);
                setFileInputAttributes(fileType);
                if (fileType === 'image') {
                    displayImagePreview(file);
                }

                $('.remove__file').click(function (e) {
                    e.preventDefault();
                    $("#add_media").html('');
                    setDefaultFileInputAttributes();
                });
            });

            function setDefaultFileInputAttributes() {
               
                $('#uploadfile input[type="file"]').val('');
                $('#uploadfile input[type="file"]').attr({
                    'name': '',
                    'id': 'media_upload',
                    'accept': ''
                });
                $(".media_upload_label").attr('for', 'media_upload');
            }
            function bytesToSize(bytes) {
                var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                if (bytes == 0) return '0 Byte';
                var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
                return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
            }

            function formatDate(date) {
                var options = {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: 'numeric',
                    second: 'numeric',
                };
                return date.toLocaleString('en-US', options);
            }

            function getFileIcon(fileType) {
                
                switch (getFileType(fileType)) {
                    case 'image':
                        return '<div class="image__preview"><img src="" alt=""></div>';
                    case 'audio': 
                        return '<i class="fs-1 fa-regular fa-file-audio"></i>';
                    case 'video':
                        return '<i class="fs-1 fa-regular fa-file-video"></i>';
                    default:
                        return '<i class="fs-1 fa-regular fa-file"></i>';
                }
            }

            function getFileType(fileType) {
                if (fileType.startsWith('image/')) {
                    return 'image';
                } else if (fileType.startsWith('audio/')) {
                    return 'audio';
                } else if (fileType.startsWith('video/')) {
                    return 'video';
                } else if (fileType === 'application/pdf' || fileType === 'application/msword' || fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                    return 'document';
                } else {
                    return 'other';
                }
            }

            function setFileInputAttributes(fileType) {
                var fileInput = $('#media_upload');
                switch (fileType) {
                    case 'video':
                        fileInput.attr({
                            'name': 'video',
                            'id':'video',
                            'accept': '.mp4,.mov,.avi'
                        });
                        break;
                    case 'audio':
                        fileInput.attr({
                            'name': 'audio',
                            'id': 'audio',
                            'accept': '.mp3,.wav'
                        });
                        break;
                    case 'document':
                        fileInput.attr({
                            'name': 'document',
                            'id': 'document',
                            'accept': '.doc,.docx,.pdf'
                        });
                        break;
                    case 'image':
                        fileInput.attr({
                            'name': 'image',
                            'id': 'image',
                            'accept': '.jpg,.jpeg,.png,.gif'
                        });
                        break;
                    default:
                        fileInput.attr({
                            'name': '',
                            'id': 'media_upload'
                        });
                        break;
                }
                var labelFor = fileInput.attr('id');
                $(".media_upload_label").attr('for', labelFor);
            }

            function displayImagePreview(file) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.image__preview img').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }  
        });
		const selectType = $('#selectTypeChange');
		const fileInput = $('#uploadfile');

		selectType.on('change', () => {
			const selectedValue = selectType.val();
			switch (selectedValue) {
			  case 'file':
			    fileInput.html('<input class="form-control" type="file" name="document" id="document" accept=".doc,.docx,.pdf">');
			    break;
			  case 'image':
			  	fileInput.html('<input class="form-control" type="file" name="image" id="image" accept=".jpg,.jpeg,.png,.gif">');
			    break;
			  case 'audio':
			  	fileInput.html('<input class="form-control" type="file" name="audio" id="audio" accept=".mp3,.wav">');
			    break;
			  case 'video':
			  	fileInput.html('<input class="form-control" type="file" name="video" id="video" accept=".mp4,.mov,.avi">');
			    break;
			  default:
			  	fileInput.html('<input class="form-control" type="file" name="" id="file">');
			    break;
			}
		});

		const inputNumber = document.getElementById('number');
		inputNumber.addEventListener('keyup', function() {
            this.value = this.value.replace(/[^\d.-]/g, '');
		});

        // Note
        const infoNoteBtn = document.querySelector(".info-note-btn");
        infoNoteBtn.addEventListener("click", ()=>{
                const noteContainer = document.querySelector(".note-container");
                noteContainer.classList.toggle("d-none");
        })

        //Whatsapp Style
        $(document).ready(function () {
            $('.style-link').on('click', function (e) {
                e.preventDefault();
                var style = $(this).data('style');
                var textarea = $('#message')[0];
                var selectedText = textarea.value.substring(textarea.selectionStart, textarea.selectionEnd);

                if (selectedText.trim() === '') {
                    return;
                }

                var startChar = '';
                var endChar = '';

                switch (style) {
                    case 'bold':
                        startChar = '*';
                        endChar = '*';
                        break;
                    case 'italic':
                        startChar = '_';
                        endChar = '_';
                        break;
                    case 'mono':
                        startChar = '```';
                        endChar = '```';
                        break;
                    case 'strike':
                        startChar = '~';
                        endChar = '~';
                        break;
                }

                var startOffset = textarea.selectionStart;
                var endOffset = textarea.selectionEnd;

                var modifiedText = startChar + selectedText + endChar;

                // Set the modified text and adjust the selection range
                textarea.setRangeText(modifiedText, startOffset, endOffset, 'end');
                textarea.setSelectionRange(startOffset + startChar.length, startOffset + startChar.length + selectedText.length + endChar.length);
            });
        });
	})(jQuery);


</script>
@endpush

