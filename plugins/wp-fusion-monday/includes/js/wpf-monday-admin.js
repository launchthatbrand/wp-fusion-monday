jQuery(document).ready(function ($) {
  var syncTags = function (button, total, crmContainer) {
    button.addClass("button-primary");
    button.find("span.dashicons").addClass("wpf-spin");
    button.find("span.text").html(wpf_ajax.strings.syncTags);

    var data = {
      action: "wpf_sync",
      _ajax_nonce: wpf_ajax.nonce,
    };

    $.post(ajaxurl, data, function (response) {
      if (response.success == true) {
        if (true == wpf_ajax.connected) {
          // If connection already configured, skip users sync
          button.find("span.dashicons").removeClass("wpf-spin");
          button.find("span.text").html("Complete");
        } else {
          button.find("span.text").html(wpf_ajax.strings.loadContactIDs);

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

  var syncLists = function (button, total, crmContainer) {
    console.log("syncLists init");
    button.addClass("button-primary");
    button.find("span.dashicons").addClass("wpf-spin");
    button.find("span.text").html("Syncing Workspaces");

    var data = {
      action: "wpf_sync_lists",
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

  // Sync workspaces
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
    postFields = $(button).attr("data-post-fields").split(",");

    console.log("postFields:", postFields);

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
        console.log("connection success, determining sub-function");
        $(crmContainer).find("div.error").remove();
        $("#wpf-needs-setup").slideUp(400);
      }

      // Determine which sub-function to trigger based on postFields
      if (postFields.includes("monday_workspace")) {
        // Call syncTagFields if monday_board is provided
        syncLists(button, crmContainer);
      } else if (postFields.includes("monday_board")) {
        // Call syncTagFields if monday_board is provided
        syncTagFields(button, crmContainer);
      } else if (postFields.includes("monday_tag_field")) {
        // Call syncTagFields if monday_tag is provided
        syncTags(button, crmContainer);
      } else if (
        postFields.includes("monday_url") &&
        postFields.includes("monday_key")
      ) {
        // Call syncWorkspaces if monday_url and monday_key are provided
        syncWorkspaces(button, crmContainer);
      }

      // remove disabled on submit button:
      $('p.submit input[type="submit"]').removeAttr("disabled");
    });
  });

  $('select[name="wpf_options[monday_board]"]').select4({
    placeholder: "Select Board",
    allowClear: true,
    minimumResultsForSearch: 1, // This enables the search box
  });

  $("#doaction, #doaction2").on("click", function (e) {
    if (
      $('select[name="action"]').val() == "export_selected_users" ||
      $('select[name="action2"]').val() == "export_selected_users"
    ) {
      e.preventDefault();
      console.log("export_selected_users");

      var user_ids = [];
      $('input[name="users[]"]:checked').each(function () {
        user_ids.push($(this).val());
      });

      var data = {
        action: "export_selected_users",
        _ajax_nonce: wpf_ajax.nonce, // Use the existing nonce
        user_ids: user_ids,
      };

      $.post(ajaxurl, data, function (response) {
        if (response.success) {
          alert(response.data.message);
          // Optionally, reload the page or show a progress bar here
        } else {
          alert("Failed: " + response.data.message);
        }
      });
    }
  });
});
