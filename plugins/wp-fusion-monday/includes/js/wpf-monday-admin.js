jQuery(document).ready(function ($) {
  // Sync tags and custom fields

  var syncWorkspaces = function (button, total, crmContainer) {
    console.log("syncWorkspaces init");
    button.addClass("button-primary");
    button.find("span.dashicons").addClass("wpf-spin");
    button.find("span.text").html("Syncing Workspaces");

    var data = {
      action: "wpf_sync_workspaces",
      _ajax_nonce: wpf_ajax.nonce,
    };

    $.post(ajaxurl, data, function (response) {
      if (response.success == true) {
        if (true == wpf_ajax.connected) {
          // If connection already configured, skip users sync
          button.find("span.dashicons").removeClass("wpf-spin");
          button.find("span.text").html("Complete");
        } else {
          button.find("span.text").html("Loading Contact IDs");

          var data = {
            action: "wpf_batch_init",
            _ajax_nonce: wpf_ajax.nonce,
            hook: "users_sync",
          };

          $.post(ajaxurl, data, function (total) {
            //getBatchStatus(total, 'Users (syncing contact IDs and tags, no data is being sent)');
            wpf_ajax.connected = true;
            button.find("span.dashicons").removeClass("wpf-spin");
            button.find("span.text").html("Complete");

            $(crmContainer)
              .find("#connection-output")
              .html(
                '<div class="updated"><p>' +
                  wpf_ajax.strings.connectionSuccess.replace(
                    "CRMNAME",
                    $(crmContainer).attr("data-name")
                  ) +
                  "</p></div>"
              );
          });
        }
      } else {
        $(crmContainer)
          .find("#connection-output")
          .html(
            '<div class="error"><p><strong>' +
              wpf_ajax.strings.error +
              ": </strong>" +
              response.data +
              "</p></div>"
          );
      }
    });
  };

  // Button handler for test connection / resync

  $("a#test-monday-connection").on("click", function () {
    console.log("test monday connection click");
    var button = $(this);
    var crmContainer = $("div.crm-config.crm-active");

    button.addClass("button-primary");
    button.find("span.dashicons").addClass("wpf-spin");
    button.find("span.text").html(wpf_ajax.strings.connecting);

    var crm = $(crmContainer).attr("data-crm");

    var data = {
      action: "wpf_test_connection_" + crm,
      _ajax_nonce: wpf_ajax.nonce,
    };

    // Add the submitted data
    postFields = $(crmContainer)
      .find("#test-monday-connection")
      .attr("data-post-fields")
      .split(",");

    $(postFields).each(function (index, el) {
      if ($("#" + el).length) {
        data[el] = $("#" + el).val();
      }
    });

    // Log the postFields array to the console
    console.log("data:", data);

    // Test the CRM connection

    $.post(ajaxurl, data, function (response) {
      if (response.success != true) {
        $("li#tab-setup a").trigger("click"); // make sure we're on the Setup tab

        $(crmContainer)
          .find("#connection-output")
          .html(
            '<div class="error"><p><strong>' +
              wpf_ajax.strings.error +
              ": </strong>" +
              response.data +
              "</p></div>"
          );

        button.find("span.dashicons").removeClass("wpf-spin");
        button.find("span.text").html("Retry");
      } else {
        console.log("connection success, sync workspaces");
        $(crmContainer).find("div.error").remove();

        $("#wpf-needs-setup").slideUp(400);
        var total = parseFloat(button.attr("data-total-users"));
        syncWorkspaces(button, total, crmContainer);

        // remove disabled on submit button:

        $('p.submit input[type="submit"]').removeAttr("disabled");
      }
    });
  });

  $('select[name="wpf_options[monday_board]"]').select4({
    placeholder: "Select Board",
    allowClear: true,
    minimumResultsForSearch: 1, // This enables the search box
  });
});
