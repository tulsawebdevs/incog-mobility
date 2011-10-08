/*jslint white: true, browser: true, devel: true, onevar: true, undef: true, nomen: true, eqeqeq: true, plusplus: true, bitwise: true, regexp: true, newcap: true, immed: true */
/*global $ */

var oneCare = oneCare || {};

// Add custom filter to dataTables before init
function hideOldProjects(oSettings, aData, iDataIndex) {
	if ($("#toggleOld").is(":checked")) {
		// User has set the filter
		if (aData[3] === "Completed" || aData[3] === "Abandoned") {
			// Hide matching rows
			return false;
		}
	} else {
		return true;
	}

	return true;
}

oneCare.CONFIG = {
	dashboardTable: {
		"aoColumnDefs": [{
			"aTargets": [4],
			"sWidth": "92px"
		}, {
			"aTargets": [5],
			"bSearchable": false,
			"bSortable": false,
			"sClass": "center"
		}],
		"aaSorting": [[4, "asc"]],
		"bJQueryUI": true,
		"bRetrieve": true,
		"bStateSave": true
	},
	
	dashboardTabs: {
		cookie: { expires: 30 },
		show: function (event, ui) {
			$("table.projectTable").show();
			$.fn.dataTableExt.afnFiltering.push(hideOldProjects);
			var oTable = $("table.projectTable", ui.panel).dataTable(oneCare.CONFIG.dashboardTable);
			if (oTable.length) {
				oTable.fnAdjustColumnSizing();
				
				// Create a global var for filtering later
				oneCare.requestDashboard.tbl = oTable;
			}
		}
	},
	
	datepicker: {
		buttonImage: "/img/icons/calendar.png",
		buttonImageOnly: true,
		changeMonth: true,
		changeYear: true,
		onClose: function () {
			$(this).focus();
		},
		onSelect: function () {
			$(this).focus();
		},
		selectOtherMonths: true,
		showOn: "button",
		showOtherMonths: true
	},
	
	projectTypes: {
		// Project types and the additional fields to enable for each
		typePackageConcept: {
			aUpcCode: 1,
			aItemNumber: 1,
			aPartNumber: 0,
			aPackageDimensions: 1,
			aPrinter: 1,
			aContactInformation: 1,
			aStock: 1,
			aVarnish: 1,
			aDieReference: 1,
			aCount: 0,
			aItemDimensions: 1,
			aColorLimit: 1
		},
		typePackageRevision: {
			aUpcCode: 1,
			aItemNumber: 1,
			aPartNumber: 1,
			aPackageDimensions: 1,
			aPrinter: 1,
			aContactInformation: 1,
			aStock: 1,
			aVarnish: 1,
			aDieReference: 1,
			aCount: 1,
			aItemDimensions: 1,
			aColorLimit: 1
		},
		typeAdConcept: {
			aUpcCode: 1,
			aItemNumber: 1,
			aPartNumber: 1,
			aPackageDimensions: 1,
			aPrinter: 1,
			aContactInformation: 1,
			aStock: 0,
			aVarnish: 1,
			aDieReference: 1,
			aCount: 1,
			aItemDimensions: 1,
			aColorLimit: 1
		},
		typeDielineRequest: {
			aUpcCode: 1,
			aItemNumber: 0,
			aPartNumber: 0,
			aPackageDimensions: 1,
			aPrinter: 0,
			aContactInformation: 0,
			aStock: 0,
			aVarnish: 0,
			aDieReference: 1,
			aCount: 0,
			aItemDimensions: 1,
			aColorLimit: 0
		},
		typeMechanicalArttoSupplier: {
			aUpcCode: 1,
			aItemNumber: 0,
			aPartNumber: 1,
			aPackageDimensions: 1,
			aPrinter: 1,
			aContactInformation: 1,
			aStock: 0,
			aVarnish: 1,
			aDieReference: 1,
			aCount: 1,
			aItemDimensions: 1,
			aColorLimit: 1
		},
		typeMockup: {
			aUpcCode: 1,
			aItemNumber: 0,
			aPartNumber: 0,
			aPackageDimensions: 0,
			aPrinter: 0,
			aContactInformation: 0,
			aStock: 0,
			aVarnish: 0,
			aDieReference: 1,
			aCount: 1,
			aItemDimensions: 1,
			aColorLimit: 1
		},
		typeCoupon: {
			aUpcCode: 1,
			aItemNumber: 1,
			aPartNumber: 1,
			aPackageDimensions: 0,
			aPrinter: 1,
			aContactInformation: 1,
			aStock: 0,
			aVarnish: 1,
			aDieReference: 1,
			aCount: 1,
			aItemDimensions: 1,
			aColorLimit: 1
		},
		typePresentationGraphics: {
			aUpcCode: 1,
			aItemNumber: 0,
			aPartNumber: 0,
			aPackageDimensions: 0,
			aPrinter: 1,
			aContactInformation: 1,
			aStock: 0,
			aVarnish: 1,
			aDieReference: 0,
			aCount: 1,
			aItemDimensions: 0,
			aColorLimit: 1
		},
		typeJPGorPDF: {
			aUpcCode: 1,
			aItemNumber: 0,
			aPartNumber: 0,
			aPackageDimensions: 0,
			aPrinter: 0,
			aContactInformation: 0,
			aStock: 0,
			aVarnish: 1,
			aDieReference: 0,
			aCount: 0,
			aItemDimensions: 1,
			aColorLimit: 1
		},
		typeDisplay: {
			aUpcCode: 1,
			aItemNumber: 1,
			aPartNumber: 1,
			aPackageDimensions: 1,
			aPrinter: 0,
			aContactInformation: 0,
			aStock: 1,
			aVarnish: 1,
			aDieReference: 0,
			aCount: 1,
			aItemDimensions: 1,
			aColorLimit: 1
		},
		typeIllustration: {
			aUpcCode: 1,
			aItemNumber: 0,
			aPartNumber: 0,
			aPackageDimensions: 0,
			aPrinter: 1,
			aContactInformation: 1,
			aStock: 1,
			aVarnish: 0,
			aDieReference: 0,
			aCount: 0,
			aItemDimensions: 1,
			aColorLimit: 1
		},
		typePhotography: {
			aUpcCode: 1,
			aItemNumber: 0,
			aPartNumber: 0,
			aPackageDimensions: 1,
			aPrinter: 1,
			aContactInformation: 1,
			aStock: 1,
			aVarnish: 0,
			aDieReference: 0,
			aCount: 1,
			aItemDimensions: 1,
			aColorLimit: 1
		},
		typePackageRendering3D: {
			aUpcCode: 1,
			aItemNumber: 1,
			aPartNumber: 1,
			aPackageDimensions: 1,
			aPrinter: 0,
			aContactInformation: 0,
			aStock: 0,
			aVarnish: 0,
			aDieReference: 0,
			aCount: 0,
			aItemDimensions: 0,
			aColorLimit: 0
		},
		typePlanogram: {
			aUpcCode: 1,
			aItemNumber: 0,
			aPartNumber: 0,
			aPackageDimensions: 1,
			aPrinter: 0,
			aContactInformation: 0,
			aStock: 0,
			aVarnish: 0,
			aDieReference: 0,
			aCount: 1,
			aItemDimensions: 0,
			aColorLimit: 0
		},
		typeTradeshow: {
			aUpcCode: 1,
			aItemNumber: 0,
			aPartNumber: 0,
			aPackageDimensions: 0,
			aPrinter: 1,
			aContactInformation: 1,
			aStock: 0,
			aVarnish: 1,
			aDieReference: 1,
			aCount: 1,
			aItemDimensions: 0,
			aColorLimit: 0
		},
		typeOther: {
			aUpcCode: 1,
			aItemNumber: 1,
			aPartNumber: 1,
			aPackageDimensions: 1,
			aPrinter: 1,
			aContactInformation: 1,
			aStock: 1,
			aVarnish: 1,
			aDieReference: 1,
			aCount: 1,
			aItemDimensions: 1,
			aColorLimit: 1
		}
	},
	
	validator: {
		errorElement: "span",
		errorPlacement: function (error, element) {
			if (element.is(":radio")) {
				element.closest("dl").find("dt").append(error);
			} else {
				error.insertBefore(element);
			}
		},
		rules: {
			date: {
				date: true
			}
		},
		submitHandler: function (form) {
			oneCare.requestAdd.prepFormForSubmit();
			form.submit();
		}
	}
};