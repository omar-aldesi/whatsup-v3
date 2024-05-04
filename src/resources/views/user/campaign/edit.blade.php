@extends('user.layouts.app')
@section('panel')
<section>
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">{{translate('Edit SMS Campaign')}}</h4>
			<div  data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Suggestions Note">
				<button class="i-btn info--btn btn--sm d-xl-none info-note-btn"><i class="las la-info-circle"></i></button>
			</div>
		</div>

		<div class="card-body position-relative">
			<form action="{{route('user.campaign.update')}}" method="POST" enctype="multipart/form-data">
			   @csrf
			   <div class="row g-4">
					 <div class="col-xl-9 order-xl-1 order-2">
							<div class="form-wrapper">
								<h6 class="form-wrapper-title">{{$channel}}{{translate('Set Target Audience')}}</h6>

								<input type="hidden" name="channel" value="{{@$channel}}">
								<input type="hidden" name="id" value="{{@$campaign->id}}">

								<div class="row g-4">
									<div class="col-md-6">
										<div class="form-item">
											<label class="form-label" for="name">{{ translate('Name')}}  <sup class="text-danger">*</sup></label>
											<input class="form-control campaign-name" type="text" name="name" id="name" placeholder="{{translate('Enter Name')}}" value="{{$campaign->name}}">
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-item">
											<label class="form-label" for="status">{{ translate('Status')}}</label>
											<select class="form-select campaign-status" name="status" id="status">
												<option value="" disabled="">{{ translate('Select One')}}</option>
												<option {{$campaign->status == "Active" ? "selected" :""}} value="Active">{{translate("Active")}}</option>
												<option {{$campaign->status == "DeActive" ? "selected" :""}} value="DeActive">{{translate("DeActive")}}</option>
											</select>

											<div class="form-text">
												{{ translate('Can be select single or multiple group')}}
											</div>
										</div>
									</div>
								</div>

								<div class="file-tab mt-4">
									<ul class="nav nav-tabs mb-3 gap-2" id="myTabContent" role="tablist">
										<li class="nav-item group-audience" role="presentation">
											<button class="nav-link active" id="group-tab" data-bs-toggle="tab" data-bs-target="#group-tab-pane" type="button" role="tab" aria-controls="group-tab-pane" aria-selected="false"><i class="las la-users"></i> {{ translate('Group Audience') }}</button>
										</li>
										<li class="nav-item import-file" role="presentation">
											<button class="nav-link" id="file-tab" data-bs-toggle="tab" data-bs-target="#file-tab-pane" type="button" role="tab" aria-controls="file-tab-pane" aria-selected="false"><i class="las la-file-import"></i> {{ translate('Import File') }}</button>
										</li>
									</ul>

									<div class="tab-content" id="myTabContent">
										<div class="tab-pane fade show active" id="group-tab-pane" role="tabpanel" aria-labelledby="group-tab" tabindex="0">
											<div class="form-item">
                                                <label class="form-label" for="group_id">{{ translate('From Group')}}</label>
                                                <select class="form-control keywords" name="group_id[]" id="group_id" multiple="multiple">
                                                    <option value="" disabled="">{{ translate('Select One')}}</option>
                                                    @foreach($groups as $group)
                                                        <option {{ $group->id == $campaign->group_id ? "selected":""  }} value="{{$group->id}}">{{$group->name}}</option>
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
														<span class="upload-browse">{{ translate('Upload File Here ') }}</span>
														</div>
													</label>
												</div>

												<div class="form-text mt-3">
														{{ translate('Download Sample: ')}}
														@if($channel == \App\Models\Campaign::EMAIL)
															<a href="{{route('demo.email.file.download', 'csv')}}" class="badge badge--primary"><i class="fa fa-download" aria-hidden="true"></i> {{ translate('csv')}}, </a>
															<a href="{{route('demo.email.file.download', 'xlsx')}}" class="badge badge--primary"><i class="fa fa-download" aria-hidden="true"></i> {{ translate('xlsx')}}</a>
														@else
															<a href="{{route('demo.file.download', 'csv')}}" class="badge badge--primary"><i class="fa fa-download" aria-hidden="true"></i> {{ translate('csv')}}, </a>
															<a href="{{route('demo.file.download', 'xlsx')}}" class="badge badge--primary"><i class="fa fa-download" aria-hidden="true"></i> {{ translate('xlsx')}}</a>
														@endif
												</div>
											</div>
										</div>
									</div>
								</div>

							</div>

							@if($channel == \App\Models\Campaign::EMAIL)
								<div class="form-wrapper">
									<h6 class="form-wrapper-title"> {{ucfirst($channel)}} {{ translate(' Header Information')}}</h6>

									<div class="row g-4">
										<div class="col-xl-4 col-md-6">
											<div class="form-item">
												<label class="form-label" for="subject">{{ translate('Subject')}} <sup class="text-danger">*</sup></label>
												<input type="text"  value="{{$campaign->subject}}" name="subject" id="subject" class="form-control" placeholder="{{ translate('Write email subject here')}}">
											</div>
										</div>

										<div class="col-xl-4 col-md-6">
											<div class="form-item">
												<label class="form-label" for="from_name">{{ translate('Send From')}}</label>
												<input class="form-control" value="{{$campaign->from_name}}" placeholder="{{ translate('Sender Name (Optional)')}}" type="text" name="from_name" id="from_name">
											</div>
										</div>

										<div class="col-xl-4 col-md-6">
											<div class="form-item">
												<label class="form-label" for="reply_to_email">{{ translate('Reply To Email')}}</label>
												<input class="form-control" value="{{$campaign->reply_to_email}}" type="Email" placeholder="{{ translate('Reply To Email (Optional)')}}" name="reply_to_email" id="reply_to_email">
											</div>
										</div>
									</div>
								</div>
							@endif


							<div class="form-wrapper">
								<h6 class="form-wrapper-title"> {{ucfirst($channel)}} {{ translate(' Body')}}</h6>
								@if($channel == \App\Models\Campaign::EMAIL)
									<div class="form-item">
										<label class="form-label" for="message">{{ translate('Message Body')}} <sup class="text-danger">*</sup></label>
										<textarea class="form-control message" name="message" id="message">{{$campaign->body}}</textarea>
										<div class="d-flex align-items-center justify-content-end mt-4">
											<a href="javascript:void(0);" id="selectEmailTemplate"
												class="i-btn info--btn btn--sm">
											{{translate('Use Email Template')}}
											</a>
										</div>
									</div>
								@else

								<div>
									@if($channel == \App\Models\Campaign::SMS)
										<div class="form-item mb-4">
											<label class="form-label">
												{{ translate('Select SMS Type')}} <sup class="text-danger">*</sup>
											</label>
											<div class="radio-buttons-container message-type">
												<div class="radio-button">
													<input class="radio-button-input" {{$campaign->sms_type == "plain" ?'checked' :"" }}  type="radio" name="smsType" id="smsTypeText" value="plain" checked="">
													<label class="radio-button-label" for="smsTypeText"><span class="radio-button-custom"></span>{{ translate('Text')}}</label>
												</div>

												<div class="form-check form-check-inline">
													<input {{$campaign->sms_type == "unicode" ?'checked' :"" }} class="radio-button-input" type="radio" name="smsType" id="smsTypeUnicode" value="unicode">
													<label class="radio-button-label" for="smsTypeUnicode"><span class="radio-button-custom"></span>{{ translate('Unicode')}}</label>
												</div>
											</div>
										</div>
									@endif

									<div class="form-item">
										<label class="form-label" for="sms-message">{{ translate('Write Message')}} <sup class="text-danger">*</sup></label>
										@if($channel == \App\Models\Campaign::WHATSAPP)
                                            <div class="my-2 d-flex flex-wrap align-items-center gap-2">
                                                <label for="media_upload" class="media_upload_label">
                                                    <div id="uploadfile">
                                                        <input type="file" id="media_upload" hidden>
                                                    </div>
                                                    
                                                    <div class="i-btn light--btn btn--sm">
                                                        {{ translate("Add Media") }}</span><i class="fa-solid fa-paperclip"></i>
                                                    </div>
                                                </label>
                                                <a title="{{ translate("Bold") }}" href="#" class="style-link i-btn light--btn btn--sm" data-style="bold"><span class="fw-bold p-0 i-btn light--btn btn--sm me-2">{{ translate("Bold") }}</span><i class="fa-solid fa-bold"></i></a>
                                                <a title="{{ translate("Italic") }}" href="#" class="style-link i-btn light--btn btn--sm" data-style="italic"><span class="fst-italic p-0 i-btn light--btn btn--sm me-2">{{ translate("Italic") }}</span><i class="fa-solid fa-italic"></i></a>
                                                <a title="{{ translate("Mono Space") }}" href="#" class="style-link i-btn light--btn btn--sm" data-style="mono"><span class="font-monospace p-0 i-btn light--btn btn--sm me-2">{{ translate("Mono Space") }}</span><i class="fa-solid fa-arrows-left-right-to-line"></i></a>
                                                <a title="{{ translate("Strike") }}" href="#" class="style-link i-btn light--btn btn--sm" data-style="strike"><span class="text-decoration-line-through p-0 i-btn light--btn btn--sm me-2">{{ translate("Strike") }}</span><i class="fa-solid fa-strikethrough"></i></a>
												<a href="javascript:void(0)" class="i-btn info--btn btn--sm ms-auto" data-bs-toggle="modal" data-bs-target="#templatedata">{{ translate('Use Template')}}</a>
											</div>
                                            <div class="custom--editor">
                                                <div class="speech-to-text" id="messageBox">
                                                    <textarea class="form-control message" name="message" id="sms-message" placeholder="{{ translate('Enter SMS Content &  For Mention Name Use ')}}@php echo "{{". 'name' ."}}"  @endphp" aria-describedby="text-to-speech-icon">{{$campaign->body}}</textarea>
                                                    <span class="voice-icon" id="text-to-speech-icon">
                                                        <i class='fa fa-microphone text-to-speech-toggle'></i>
                                                    </span>
                                                </div>
												<input type="text" id="remove_media" name="remove_media" hidden>
                                                <div id="add_media" class="test">
                                                    @if($campaign->post_data) 

                                                        <div class="file__wrapper">
                                                            <div class="file-detail">
                                                                <a href="#" class="remove__file"><i class="fa-regular fa-circle-xmark"></i></a>
																
                                                                <div>
                                                                    @if($campaign->post_data['type'] == "image")
                                                                        <div class="image__preview"><img src="{{ asset($campaign->post_data['url_file'])}}" alt="{{$campaign->post_data['name']}}"></div>
                                                                    @elseif($campaign->post_data['type'] == "audio")
                                                                        <i class="fs-1 fa-regular fa-file-audio"></i>
                                                                    @elseif($campaign->post_data['type'] == "video")
                                                                        <i class="fs-1 fa-regular fa-file-video"></i>
                                                                    @else
                                                                        <i class="fs-1 fa-regular fa-file"></i>
                                                                    @endif
                                                                </div>
                                                                <span class="file-type d-none"><i class="fa-regular fa-file-pdf"></i></span>
                                                                <div class="d-flex flex-column">
                                                                    <p title="{{$campaign->post_data['name']}}" class='fw-normal'>{{ translate('File Name: ').$campaign->post_data['name']}}</p>
                                                                    <p title="{{$campaign->post_data['type']}}">{{ translate('File Type: ').$campaign->post_data['type']}}</p>
                                                                    <p title="{{ convert_unit(filesize($campaign->post_data['url_file'])) }}">{{ translate('File Size: ').convert_unit(filesize($campaign->post_data['url_file']))}}</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($channel == \App\Models\Campaign::SMS)
                                            <div class="speech-to-text" id="messageBox">
                                                <textarea class="form-control message" name="message" id="sms-message" placeholder="{{ translate('Enter SMS Content &  For Mention Name Use ')}}@php echo "{{". 'name' ."}}"  @endphp" aria-describedby="text-to-speech-icon">{{$campaign->body}}</textarea>
                                                <span class="voice-icon" id="text-to-speech-icon">
                                                    <i class='fa fa-microphone pointer text-to-speech-toggle'></i>
                                                </span>
                                            </div>
                                        @endif
										

										<div class="mt-4 d-flex align-items-center justify-content-md-between justify-content-start flex-wrap gap-3">
											<div class="text-end message--word-count"></div>
											@if($channel == \App\Models\Campaign::SMS)
												<a href="javascript:void(0)" class="i-btn info--btn btn--sm" data-bs-toggle="modal" data-bs-target="#templatedata">{{ translate('Use Template')}}</a>
											@endif
										</div>
									</div>
								</div>

								@endif
							</div>

							<div class="form-wrapper">
								<h6 class="form-wrapper-title">{{ translate('Sending Options')}}</h6>
								<div class="row g-4">
									<div class="col-xl-4 col-md-6 ">
										<label for="schedule_date" class="form-label">
											{{translate("Schedule Date & Time")}}
											<sup class="text-danger">*</sup></label>
										<input type="datetime-local" value= "{{$campaign->schedule_time}}" name="schedule_date" id="schedule_date" class="form-control schedule-date" required="">
									</div>

									<div class="col-xl-4 col-md-6">
										<label for="repeat" class="form-label">
											{{translate('Repeat Every')}}   <sup class="text-danger">*</sup>
										</label>
										<input type="number" required id="repeatNumber" class="form-control repeat-unit" value="{{$campaign->schedule?->repeat_number}}" name="repeat_number"
											id="repeat">
									</div>

									<div class="col-xl-4 col-md-6">
										<label for="repeat-time" class="form-label">
											{{translate('Repeat in')}}   <sup class="text-danger">*</sup>
										</label>
										<select class="form-select repeat-scale" required name="repeat_format" id="repeat-time">
											<option {{$campaign->schedule?->repeat_format == 'day' ? 'selected' :""}}  value="day">{{ translate('Day') }}</option>
											<option  {{$campaign->schedule?->repeat_format == 'month' ? 'selected' :""}}    value="month">{{ translate('Month') }}</option>
											<option  {{$campaign->schedule?->repeat_format == 'year' ? 'selected' :""}}   value="year">{{ translate('Year') }}</option>
										</select>

									</div>
								</div>
							</div>

							<button type="submit" class="i-btn primary--btn btn--lg">
								{{translate("Update")}}
							</button>
					 </div>


					<div class="note-container col-xl-3 order-xl-2 order-1 d-xl-block d-none">
						<div class="note">
							<h6>{{translate('Suggestions Note')}}</h6>

							<p class="d-none group-audience-note note-message">{{translate("By selecting the 'Group Audience' input field, You can choose your personal Text Phonebook group to send or schedule messages to all of the group's contacts.")}}</p>
							<p class="d-none import-file-note note-message">{{translate("By selecting the 'Import File' input field, You can upload your local .csv or .xlsv files from your machine and send or schedule messages to those contacts")}}</p>
							<p class="d-none schedule-date-note note-message">{{translate("By selecting the 'Schedule Date' input field, You can pick date and type to send a message according to that schedule")}}</p>
							@if($channel == \App\Models\Campaign::EMAIL)
								<p class="d-none message-note note-message">{{translate("You can type your mail body here. You can use our customized text editor to make sure your mail catches the attention of your clients. Or you can bring your own custom message and paste it right here with all the designs and custom data.")}}</p>
							@else
								<p class="d-none message-note note-message">{{translate("You can either type your message or click the 'mic' icon to use the text to speech feature. By using the ")}}@php echo "{{". 'name' ."}}"  @endphp {{ translate(" variable you can mention the name for that contact. But with 'Single Audience' selected only their number will pass by that variable.") }}</p>
							@endif
							<p class="d-none message-type-note note-message">{{translate("If you select the 'Text' option $general->sms_word_text_count characters will be allocated for a single SMS. And if you select 'unicode' then $general->sms_word_unicode_count characters will be allocated for each SMS.")}}</p>
							<p class="d-none campaign-name-note note-message">{{translate("You can Edit the campaign name with this 'Campaign Name' input field.")}}</p>
							<p class="d-none repeat-unit-note note-message">{{translate("This field allows you to specify the amount of times you want this campaign message to occur in the given duration. In order to run the campaign only ones enter 0.")}}</p>
							<p class="d-none repeat-scale-note note-message">{{translate("This field allows you to specify the duration in days, months or year.")}}</p>
							<p class="d-none mail-subject-note note-message">{{translate("In this field you can add your desired E-mail subject")}}</p>
							<p class="d-none mail-send-from-note note-message">{{translate("You can add a customized name which will show up as 'send from name' in the receivers inbox")}}</p>
							<p class="d-none mail-send-email-note note-message">{{translate("You can add a customized Email which will show up as 'reply to' in the receivers inbox")}}</p>
							<p class="d-none campaign-status-note note-message">{{translate("By changing the status of this campaign, you can either active or deative it.")}}</p>
						</div>
						@php
							$plan_access = $allowed_access->type == App\Models\PricingPlan::USER;   
						@endphp
						@if($channel == \App\Models\Campaign::SMS && auth()->user()->sms_gateway == 1)
							<div class="form-wrapper mt-3">
								<h6 class="form-wrapper-title" title="{{ translate('If left unselected then the default gateway will be selected') }}">{{ translate('Select Gateway')}}</h6>
								<div class="sms-gateway">
									<label for="sms_gateway_type" class="form-label">{{translate('SMS Gateway Type')}}</label>
									@if($plan_access)
										<select class="form-select sms_gateway_type" name="gateway_type" id="sms_gateway_type">
											<option selected value="">{{ translate('-- Choose One --') }}</option>
										
											@foreach($credentials as $key=>$credential)
											@foreach($user->runningSubscription()->currentPlan()->sms->allowed_gateways as $key => $value)
													@if(preg_replace('/_/','',$key) == preg_replace('/ /','',strtoupper($credential->name)))
														<option value="{{$credential->gateway_code}}">{{strtoupper($credential->name)}}</option>
													@endif
												@endforeach
											@endforeach
										</select>
									@else
										<select class="form-select sms_gateway_type" name="gateway_type" id="sms_gateway_type">
											<option selected value="">{{ translate('-- Choose One --') }}</option>
										
											@foreach($credentials as $key=>$credential)
												<option value="{{$credential->gateway_code}}">{{strtoupper($credential->name)}}</option>
											@endforeach
										</select>
									@endif
								</div>
								<div class="sms-gateway sms-gateways mt-4 d-none">
									<label for="gatewwayId" class="form-label">{{translate('Sms Gateway')}} <sup class="text-danger">*</sup></label>
									<select class="form-control gateway-collect" name="gateway_id" id="gatewwayId"></select>
								</div>
							</div>
						@elseif($channel == \App\Models\Campaign::EMAIL)
						
							@php
								$jsonArray = json_encode($credentials);
								
							@endphp
							<div class="form-wrapper mt-3">
								<h6 class="form-wrapper-title" title="{{ translate('If left unselected then the default gateway will be selected') }}">{{ $plan_access ? translate("Select User's Email Gateway") : translate("Select Admin's Email Gateway")}}</h6>
								<div class="mail-gateway">
									<label for="gateway_type" class="form-label">{{translate('Mail Gateway Type')}}</label>
									@if($plan_access)
										<select class="form-select mail_gateway_type" name="gateway_type" id="gateway_type">
											<option selected value="">{{ translate('-- Choose One --') }}</option>
											@foreach($credentials as $credential_key=>$credential)
											@foreach($user->runningSubscription()->currentPlan()->email->allowed_gateways as $key => $value)
													@if(preg_replace('/_/','',$key) == preg_replace('/ /','',($credential_key)))
														<option value="{{strToLower($key)}}">{{strtoupper($key)}}</option>
													@endif
												@endforeach
											@endforeach
										</select>
									@else
										<select class="form-select mail_gateway_type" name="gateway_type" id="gateway_type">
											<option selected value="">{{ translate('-- Choose One --') }}</option>
											@foreach($credentials as $key=>$credential)
												<option value="{{strToLower($key)}}">{{strtoupper($key)}}</option>
											@endforeach
										</select>
									@endif
								</div>
								<div class="mail-gateway mail-gateways mt-4 d-none">
									<label for="gatewwayId" class="form-label">{{translate('Mail Gateway')}} <sup class="text-danger">*</sup></label>
									<select class="form-control gateway-collect" name="gateway_id" id="gatewwayId"></select>
								</div>
							</div>
						@endif
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

<div class="modal fade" id="globalModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div id="modal-size" class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title"></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="modal-body" class="modal-body">

            </div>
        </div>
    </div>
</div>

@endsection


@push('script-push')
<script>
	(function($){
		"use strict";

		const modal = $('#globalModal');
		$('.keywords').select2({
			tags: true,
			tokenSeparators: [',']
		});

		$(document).on('click','#use-template',function(e){
			var html  = $(this).attr('data-html')
			const domElement = document.querySelector( '.ck-editor__editable' );
			const emailEditorInstance = domElement.ckeditorInstance;
			emailEditorInstance.setData( html );
			modal.modal('hide');
        })

		//Contact File Input Details
		$("#file").change(function() {

			var contact_file = this.files[0];
			var file_name = "{{ translate('Selected: ') }}<p class='badge badge--primary'>"+ contact_file.name +"</p>";
			$("#contact_file_name").html(file_name);
		})

		$('.mail_gateway_type').change(function () {
	
			var selectedType = $(this).val();
			$('.mail-gateway').removeClass('d-none');
			$('.mail-gateway').addClass('d-block');

			if(selectedType == ''){
				$('.mail-gateways').addClass('d-none');
			}
			$.ajax({
				type: 'GET',
				url: "{{route('user.manage.email.gateway.select2')}}",
				data:{
					'type' : selectedType,
				},
				dataType: 'json',
				success: function (data) {
					
				
					$('.gateway-collect').empty();

					$.each(data, function (key, value) {
						var select   = $('<option value="' + value.id + '">' + value.name + ' ('+value.address+')</option>');
						$('.gateway-collect').append(select);
					});
				},
				error: function (xhr, status, error) {
					console.log(error);
				}
			});
		});

		$('.sms_gateway_type').change(function () {
			
			var selectedType = $(this).val();
			$('.sms-gateways').removeClass('d-none');
			$('.sms-gateways').addClass('d-block');

			if(selectedType == ''){
				$('.sms-gateways').addClass('d-none');
			}
			$.ajax({
				type: 'GET',
				url: "{{route('user.sms.gateway.select2')}}",
				data:{
					'type' : selectedType,
				},
				dataType: 'json',
				success: function (data) {  
					
				
					$('.gateway-collect').empty();

					$.each(data, function (key, value) {
						console.log(value);
					
						var select   = $('<option value="' + value.id + '">' + value.name + '</option>');
						$('.gateway-collect').append(select);
					});
				},
				error: function (xhr, status, error) {
					console.log(error);
				}
			});
		});
        if("{{$channel}}" == "{{\App\Models\Campaign::EMAIL}}"){
			$(document).ready(function() {

				CKEDITOR.ClassicEditor.create(document.getElementById("message"), {
					placeholder: document.getElementById("message").getAttribute("placeholder"),
					toolbar: {
					items: [
						'heading',
						'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
						'alignment', '|',
						'bold', 'italic', 'strikethrough', 'underline', 'subscript', 'superscript', 'removeFormat', 'findAndReplace', '-',
						'bulletedList', 'numberedList', '|',
						'outdent', 'indent', '|',
						'undo', 'redo',
						'link', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', '|',
						'horizontalLine', 'pageBreak', '|',
						'sourceEditing'
					],
					shouldNotGroupWhenFull: true
					},
					list: {
					properties: {
						styles: true,
						startIndex: true,
						reversed: true
					}
					},
					heading: {
					options: [
						{ model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
						{ model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
						{ model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
						{ model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
						{ model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
						{ model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
						{ model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
					]
					},
					fontFamily: {
					options: [
						'default',
						'Arial, Helvetica, sans-serif',
						'Courier New, Courier, monospace',
						'Georgia, serif',
						'Lucida Sans Unicode, Lucida Grande, sans-serif',
						'Tahoma, Geneva, sans-serif',
						'Times New Roman, Times, serif',
						'Trebuchet MS, Helvetica, sans-serif',
						'Verdana, Geneva, sans-serif'
					],
					supportAllValues: true
					},
					fontSize: {
					options: [10, 12, 14, 'default', 18, 20, 22],
					supportAllValues: true
					},
					htmlSupport: {
					allow: [
						{
						name: /.*/,
						attributes: true,
						classes: true,
						styles: true
						}
					]
					},
					htmlEmbed: {
					showPreviews: true
					},
					link: {
					decorators: {
						addTargetToExternalLinks: true,
						defaultProtocol: 'https://',
						toggleDownloadable: {
						mode: 'manual',
						label: 'Downloadable',
						attributes: {
							download: 'file'
						}
						}
					}
					},
					mention: {
					feeds: [
						{
						marker: '@',
						feed: [
							'@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
							'@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
							'@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
							'@sugar', '@sweet', '@topping', '@wafer'
						],
						minimumCharacters: 1
						}
					]
					},
					removePlugins: [
					'CKBox',
					'CKFinder',
					'EasyImage',
					'RealTimeCollaborativeComments',
					'RealTimeCollaborativeTrackChanges',
					'RealTimeCollaborativeRevisionHistory',
					'PresenceList',
					'Comments',
					'TrackChanges',
					'TrackChangesData',
					'RevisionHistory',
					'Pagination',
					'WProofreader',
					'MathType'
					]
				});
			});
		}


		$(document).on('click','#selectEmailTemplate',function(e){
			$("#selectEmailTemplate").html('{{translate("Template Loading...")}}');
			appendTemplate()
			e.preventDefault()
        })

		//load pre-build template method start
		function  appendTemplate(){
			$.ajax({
				method:"GET",
				url:"{{ route('user.template.email.list') }}",
				dataType:'json'
			}).then(response=>{
				$("#selectEmailTemplate").html('{{translate("Use Email Template")}}');
				appendModalData(response.view)
			})
        }

        // append modal data method start
		function appendModalData(view){
			$('#modal-title').html(`{{translate('Pre Build Template')}}`)
			var html = `
				<div class="modal-body">
				   ${view}
				</div>
			`
			$('#modal-body').html(html)
			modal.modal('show');
		}

		if("{{$channel}}" == "{{\App\Models\Campaign::SMS}}"){
			var wordLength = {{$general->sms_word_text_count}};
			$('input[type=radio][name=smsType]').on('change', function(){
				if(this.value == "unicode"){
					wordLength = {{$general->sms_word_unicode_count}};
				}else{
					wordLength = {{$general->sms_word_text_count}};
				}
			});

			$(`textarea[name=message]`).on('keyup', function(event) {
				var character = $(this).val();
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
		}

		if("{{$channel}}" == "{{\App\Models\Campaign::WHATSAPP}}"){

			//Whatsapp Style
			
			$('.style-link').on('click', function (e) {
                e.preventDefault();
                var style = $(this).data('style');
                var textarea = $('#sms-message')[0];
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

               
            });
            $('.remove__file').click(function (e) {
                e.preventDefault();
				$("#remove_media").val('true');	
				$("#add_media").html('');
				
               
                setDefaultFileInputAttributes();
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
		}

	    $('select[name=template]').on('change', function(){
	    	var character = $(this).val();
	    	$('textarea[name=message]').val(character);
		    $('#templatedata').modal('toggle');
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

	    const inputNumber = document.getElementById('number');
		if(inputNumber){
			inputNumber.addEventListener('keyup', function() {
			const cleanedValue = this.value.replace(/[^\d.-]/g, '');
			this.value = cleanedValue;
		  });
		}
	})(jQuery);
</script>
@endpush

