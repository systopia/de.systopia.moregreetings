// TODO: inject IDs
var more_greetings_block = "div.crm-custom-set-block-MOREGREETINGS";


cj(document).ready(function () {
  // move more greetings block to right hand side
  cj("div.crm-summary-demographic-block").after(cj(more_greetings_block));

  var rows = cj(more_greetings_block).find("div.crm-summary-row");
  for (var i = 0; i < rows.length / 2; i+=2) {
    var flag = cj(rows[i+1]).find("div.crm-content").html();
    if (flag == 'LOCALISED_YES') {
      // mark protected rows:
      var greeting_label = cj(rows[i]).find("div.crm-label");
      greeting_label.html("<i>" + greeting_label.html() + "</i>");
      // var greeting = cj(rows[i]).find("div.crm-content");
      // greeting.html("<i>" + greeting.html() + "</i>");
    }

    // remove the rows showing the protection flag
    cj(rows[i+1]).remove();
  }
});

