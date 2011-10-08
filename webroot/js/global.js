/*jslint white: true, browser: true, devel: true, onevar: true, undef: true, nomen: true, eqeqeq: true, plusplus: true, bitwise: true, regexp: true, newcap: true, immed: true, sloppy: true */
/*global jQuery, $, UTIL, flowplayer, CKEDITOR */

var oneCare = oneCare || {};

oneCare.common = {
	init: function () {
		if ($.browser.msie && $.browser.version < 7) {
		    $("li.hasChildren a").css("background-image", "none");
	    } else {
	        $("li.hasChildren").hover(function () {
				$(this).find(".sub").show();
			}, function () {
				$(this).find(".sub").hide();
			});
		}
	
		$("li.noLink a").click(function (e) {
			e.preventDefault();
			return false;
		});
		
		$.standardize();
		
		$(".no-js").remove();
		
		// Enable debugging for local env
		if (window.location.hash === "#debug") {
			$("div.section").show();
			oneCare.CONFIG.validator.debug = true;
		}
	}
};

oneCare.dashboard = {
	init: function () {
		flowplayer("player", "/vendor/flowplayer/flowplayer-3.2.5.swf");
	}
};

oneCare.documents = {
	init: function () {
		// Allow click-through to directory
		$("a.hasChildren span").live("click", function (e) {
			e.stopPropagation();
		});
		
		// If not clicking the directory title, show the following list of subdirectories
		if ($.browser.msie && ($.browser.version === "6.0" || $.browser.version === "7.0")) {
			// do nothing
			return true;
		} else {
			$("a.hasChildren").live("click", function (e) {
				e.preventDefault();
				oneCare.documents.toggleDocumentList($(this));
			});
		}
	},
	
	toggleDocumentList: function (el) {
		var title = el.find("h2"),
			src = "";
			
		src = title.css("background-image");
		
		src = (src.indexOf("plus") !== -1) ? "bullet_toggle_minus" : "bullet_toggle_plus";
		title.css("background-image", "url(/img/icons/" + src + ".png)");
		
		el.next("ul").slideToggle();
	}
};

oneCare.directory = {
	init: function () {
		$("#docTable").dataTable({
			"aoColumnDefs": [{
				"aTargets": [2],
				"sWidth": "80px"
			}],
			"aaSorting": [[1, "desc"]],
			/*"aoColumns": [
				null,
				null,
				{ "sWidth": "80px" }
			],*/
			"oLanguage": {
				"sLengthMenu": "Show _MENU_ documents",
				"sInfo": "Showing _START_ to _END_ of _TOTAL_ documents",
				"sInfoEmpty": "Showing 0 to 0 of 0 documents",
				"sInfoFiltered": "(filtered from _MAX_ total documents)",
				"sSearch": "Filter documents:"
			}
		});
	}
};

oneCare.pages = {
	init: function () {
		if ($("textarea#PageContent").length) {
			CKEDITOR.replace("PageContent");
		}
		
		// Refactor if more calendars are added
		if ($("#cal1Toggle").length) {
			$("#cal1Toggle").click(function () {
				$(".toggler a").removeClass("active");
				$(this).addClass("active");
				
				$("#calendar1").show();
				$("#calendar2").hide();
			
				return false;
			});
			
			$("#cal2Toggle").click(function () {
				$(".toggler a").removeClass("active");
				$(this).addClass("active");
			
				$("#calendar2").show();
				$("#calendar1").hide();
				
				return false;
			});
		}
	}
};

// new class will be oneCare."requestAdd creative"

oneCare.requestAdd = {
	fileAttached: false,
	
	init: function () {
			
		// Must initiate validate before validating each section in showNextSection()
		$("form#RequestAddForm").validate(oneCare.CONFIG.validator);

		// Datepicker
		$("input.date").datepicker(oneCare.CONFIG.datepicker);

		// Section layout
		$("div.section:first").show();
		$("#projectName").focus();
		$("button.continueForm").click(function () {
			oneCare.requestAdd.showNextSection($(this));
		});

		// Brand selection
		$("a.brandImg").click(function (e) {
			e.preventDefault();
			oneCare.requestAdd.selectBrand($(this));
		});
		
		// Add/remove file attachments
		$("button.addItem").click(function () {
			oneCare.requestAdd.addItem($(this));
		});
		$("a.removeItem").live("click", function (e) {
			e.preventDefault();
			oneCare.requestAdd.removeItem($(this));
		});
		$("input.file").live("click", function () {
			oneCare.requestAdd.fileAttached = true;
		});
	},

	addItem: function (el) {
		var $src = el.siblings("div.hidden.src");
		
		// We need to temporarily assign names for proper validation
		// Plugin will only display error on first input if there are copies of a single name attribute
		$src.find("input").each(function () {
			$(this).attr("name", Math.random());
		});
		
		$src.contents().clone().insertBefore($src).slideDown("fast").removeClass("hidden");
	},
	
	prepFormForSubmit: function () {
		var nameAttr = "file-",
			msg = "",
			i = 0;
		
		// No duplicates
		$("button[type='submit']").attr("disabled", "disabled").attr("class", "disabled");
		
		// Make sure all attachment files have a name attribute when submitting
		$("input.file").each(function () {
			$(this).attr("name", nameAttr + i.toString());
			i += 1;
		});
		$("div.hidden.src").remove();
		
		// Display message submission message
		msg = '<div class="messaging hidden"><p class="info"><img src="/img/icons/ajax-loader.gif" /> Your form is submitting. ';
		msg += (oneCare.requestAdd.fileAttached) ? 'If the files you have attached are large, the submission may take some time to complete. ' : '';
		msg += 'Thank you for your patience.</p></div>';
		$(msg).insertAfter($("button[type='submit']")).fadeIn("fast").removeClass("hidden");
				
		// Populate submission time
		$("#submissionTime").val(Date.now());
	},

	removeItem: function (el) {		
		if (el.data("remove")) {
			// Two fields, product and UPC
			el.closest("div.field").prev().remove();
			el.closest("div.field").remove();
			return true;
		}
		
		// Single item, like file on creative
		el.parent().remove();
	},

	selectBrand: function (el) {
		var $img = el.find("img"),
			src = $img.attr("src"),
			newSrc = (src.indexOf("-on") === -1) ? src.replace(".gif", "-on.gif") : src.replace("-on.gif", ".gif");

		// Reset all other images
		$("a.brandImg img").each(function () {
			if ($(this).attr("src").indexOf("-on") !== -1) {
				src = $(this).attr("src").replace("-on.gif", ".gif");
				$(this).attr("src", src);
				$(this).css("border-color", "#fff");
				
			}
		});

		// Set new image to active
		$img.attr("src", newSrc);
		$img.css("border-color", "#999");

		// Populate hidden field
		$("input[name='data[Request][brand]']").val($img.attr("alt"));
	},

	showNextSection: function (el) {
		var $section = el.closest(".section"),
			$heading = $section.next("h2"),
			$checkFields = $section.find("input.required, select.required"),
			ok = false;

		$checkFields.each(function () {
			$("#RequestAddForm").validate().element($(this));
		});

		ok = (!$section.find(".error:visible").length) ? true : false;
		
		if (ok || !$checkFields.length) {
			$heading
				.attr("class", "expanded")
				.next("div.section").slideDown(function () {
					$.scrollTo($heading.offset().top, 300);
				});

			el.attr("disabled", "disabled").addClass("disabled");
		} else {
			return false;
		}
	}
};

oneCare.creativeRequestAdd = {
	init: function () {
		// Show/hide 'other' options
		$("#projectCategory").change(function () {
			oneCare.creativeRequestAdd.otherCategory($(this));
		});
		$("#typeOther").click(function () {
			UTIL.log("event handler");
			oneCare.creativeRequestAdd.otherType($(this));
		});
		$("button.showAdditionalFields").click(function (e) {
			e.preventDefault();
			oneCare.creativeRequestAdd.showAdditionalFields($(this));
		});
		
		// Additional details toggle
		$("ul.projectTypes input").change(function () {
			oneCare.creativeRequestAdd.updateDetailsMatrix($(this).attr("id"));
		});
	},
	
	otherCategory: function (el) {
		var $field = el.parent().next(".field"),
			$input = $field.find("input");

		if (el.val() === "other") {
			$field.removeClass("hidden");
			$input.addClass("required");
		} else {
			$field.addClass("hidden");
			$input.removeClass("required");
		}
	},
	
	otherType: function (el) {
		var $field = $("#typeOtherText");

		if (el.is(":checked")) {
			UTIL.log("is checked");
			$field.removeAttr("disabled").addClass("required");
		} else {
			UTIL.log("is not checked");
			$field.attr("disabled", "disabled").removeClass("required");
		}
	},
	
	showAdditionalFields: function (el) {
		var $wrapper = $("div.additionalFieldWrapper");
		
		$wrapper.slideDown(function () {
			$.scrollTo($wrapper.offset().top, 300);
		});
		
		el.remove();
	},
	
	updateDetailsMatrix: function (id) {
		var obj = oneCare.CONFIG.projectTypes[id],
			key = "";
		
		for (key in obj) {
			if (obj.hasOwnProperty(key) && obj[key]) {
				$("#" + key).removeAttr("disabled").closest(".field").removeClass("disabled");
			}
		}
	}
};

oneCare.operationsRequestAdd = {
	init: function () {
		// Generic handler for requiring 'other' field on selection
		$(".hasOther input:radio").click(function () {
			oneCare.operationsRequestAdd.hasOther($(this));
		});
		
		$(".hasProgressive input:radio").click(function () {
			oneCare.operationsRequestAdd.hasProgressive($(this));
		});
		
		$("input[name='projectRequestType']").change(function () {
			oneCare.operationsRequestAdd.getRequestType($(this));
		});
		
		$(".requestedMaterials input").change(function () {
			oneCare.operationsRequestAdd.toggleMaterialsDetail();
		});
	},
	
	getRequestType: function (el) {
		var val = el.val(),
				text = el.parent().text();

		// Need a smoother transition if the user has moved on but needs to change
		if ($(".projectRequestTypeWrapper").is(":visible")) {
			$(".requestType").slideUp("fast");
			$("#" + val + "Form").slideDown("fast");
		} else {
			$(".requestType").hide();
			$("#" + val + "Form").show();
		}
		
		// Update section title
		$(".requestDetailsTitle").text("for " + text);
	},
	
	hasOther: function (el) {
		var $field = el.closest("dl").find(".otherField");
		
		if (el.hasClass("otherTrigger")) {
			$field.removeAttr("disabled").addClass("required");
		} else {
			$field.attr("disabled", "disabled").removeClass("required error");
			// Required message will stick around unless we remove it manually
			$field.prev("span.error").remove();
		}
	},
	
	hasProgressive: function (el) {
		var $field = el.closest("dl").next("div.progressiveField");
		
		if (el.hasClass("progressiveTrigger")) {
			$field.slideDown("fast");
			
			$field.find("input").each(function () {
				if ($(this).data("required") === "yes") {
					$(this).addClass("required");
				}
			});
		} else {
			$field.slideUp("fast");
			
			$field.find("input").each(function () {
				$(this).removeClass("required error");
				$(this).prev("span.error").remove();
			});
		}
	},
	
	toggleMaterialsDetail: function () {
		var selected = false,
				$el = $(".requestedMaterialsDetailWrapper");
		
		$(".requestedMaterials input").each(function () {
			if ($(this).is(":checked")) {
				selected = true;
			}
		});
		
		if (selected) {
			$el.slideDown();
		} else {
			$el.slideUp();
		}
	}
};

oneCare.requestDashboard = {	
	// oneCare.requestDashboard.tbl is a global var defined in configs.js
	// will always correspond to the currently displayed table
	// there may be some nicer way of getting it out of the jQuery UI show event
	
	init: function () {		
		$("#tabs").tabs(oneCare.CONFIG.dashboardTabs);
		
		$("input.toggleTbl").click(function () {
			// Redraw table on click, custom filtering is applied before dataTable init in configs.js
			oneCare.requestDashboard.tbl.fnDraw();
		});
	}
};

oneCare.requestDetail = {
	init: function () {
		var $date = $("input.date");
		
		$("#tabs").tabs();
		
		// onSelect is the only event overridden
		$date.datepicker(oneCare.CONFIG.datepicker)
			.datepicker("option", {
				onSelect: function () {
					oneCare.requestDetail.update($date, "due_date");
				}
			})
			.change(function () {
				oneCare.requestDetail.update($(this), "due_date");
			});
			
		$("#assignedTo").change(function () {
			oneCare.requestDetail.update($(this), "assignment");
		});
		
		$("#projectDivision").change(function () {
			oneCare.requestDetail.update($(this), "division");
		});
		
		$("#projectStatus").change(function () {
			oneCare.requestDetail.update($(this), "status");
			
			if ($(this).val() === "Need More Information") {
				oneCare.requestDetail.needMoreInfoModal();
			}
		});
		
		$("button.addNote").click(function () {
			oneCare.requestDetail.addNewNote();
		});
		
		$("a.viewHistory").click(function (e) {
			e.preventDefault();
			oneCare.requestDetail.viewHistory();
		});
	},
	
	addNewNote: function () {
		var $input = $(".newNote"),
				noteText = $input.val(),
				str = "";
			
		if (!noteText) {
			return false;
		}
						
		$.ajax({
			url: "/notes/add/" + $("#requestId").val() + "/?noteText=" + noteText,
			success: function () {				
				str = '<p>' + noteText + '<br /><span class="noteMeta">';
				str += '&mdash; Added on ' + new Date() + '</span></p>';

				$("#projectNotes h3").after(str);
				$input.val("");

				$("#tabs").tabs("option", "selected", 1);
				$.scrollTo($("#projectNotes"), 300);
			}
		});
	},
	
	update: function (el, field) {
		var $loader = $("img.loader"),
			requestId = $("#requestId").val(),
			url = "/requests/update/" + requestId + "/" + field + "/" + el.val() + "/?time=" + new Date().getTime().toString(),
			msg = "";
			
		switch (field) {
			case "due_date":
				msg = "Due date";
				break;
			case "assignment":
				msg = "Assignment";
				break;
			case "division":
				msg = "Division";
				break;
			default:
				msg = "Status";
				break;
		}
				
		msg += " updated succesfully.";

		$.ajax({
			url: url,
			success: function (resp) {
				if (resp && resp === "success") {
					oneCare.requestDetail.getHistory(requestId);

					$loader.hide();

					$('<div class="messaging" style="display: none;"><p class="success">' + msg + '</p></div>')
						.insertAfter($loader)
						.fadeIn("fast")
						.delay(1500)
						.fadeOut("fast", function () {
							$(this).remove();
						});

					$("span.currentStatus").text($("#projectStatus option:selected").text());
					$("span.currentDueDate").text($("#dueDate").val());
					$("span.currentDivision").text($("#projectDivision option:selected").text());
				} else {
					msg = "An error occurred; your update was not saved.";
					$('<div class="messaging" style="display: none;"><p class="error">' + msg + '</p></div>')
						.insertAfter($loader)
						.fadeIn("fast")
						.delay(1500)
						.fadeOut("fast", function () {
							$(this).remove();
						});
				}
			}
		});
	},
	
	needMoreInfoModal: function () {
		var $modal = $("#needMoreInfoModal"),
				$msg = $(".needMoreInfoMessage");
		
		$modal.dialog({
			modal: true,
			width: 800,
			height: 300
		});
		
		// Add event handler to button to send data and display confirmation message
		$modal.find("button.sendEmail").click(function () {
			if (!$msg.val()) {
				return false;
			}
						
			$.ajax({
				type: "POST",
				url: "/requests/elaborate/" + $("#requestId").val(),
				data: "message=" + $msg.val(),
				success: function (resp) {
					if (resp && resp === "success") {
						UTIL.log("email ok");
					} else {
						UTIL.log("email error");
						UTIL.log(resp);
					}
				}
			});
			
			$(this).parent().hide().next().show();
		});
		
		// Close and reset
		$modal.find("button.close").click(function () {
			$modal.dialog("close");
			$(this).parent().hide().prev().show();
			$msg.val("");
		});
	},
	
	getHistory: function (id) {
		$.ajax({
			url: "/requests/requestHistory/" + id + "?time=" + new Date().getTime().toString(),
			success: function (resp) {
				$("#historyModal").html(resp);
			}
		});
	},
	
	viewHistory: function () {
		$("#historyModal").dialog({
			modal: true,
			width: 800,
			height: 300
		});
	}
};

/*===========================================================================
	document.ready()
===========================================================================*/
jQuery(document).ready(UTIL.loadEvents);
