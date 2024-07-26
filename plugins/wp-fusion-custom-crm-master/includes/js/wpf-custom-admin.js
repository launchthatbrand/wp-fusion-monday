jQuery(document).ready(function ($) {
  $('select[name="wpf_options[monday_board]"]').select4({
    placeholder: "Select Board",
    allowClear: true,
    minimumResultsForSearch: 1, // This enables the search box
  });
});
