(function($) {

$.fn.dataTableExt.oApi.fnGetColumnData = function ( oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty ) {
    // check that we have a column id
    if ( typeof iColumn == "undefined" ) return new Array();

    // by default we only want unique data
    if ( typeof bUnique == "undefined" ) bUnique = true;

    // by default we do want to only look at filtered data
    if ( typeof bFiltered == "undefined" ) bFiltered = true;

    // by default we do not want to include empty values
    if ( typeof bIgnoreEmpty == "undefined" ) bIgnoreEmpty = true;

    // list of rows which we're going to loop through
    var aiRows;

    // use only filtered rows
    if (bFiltered == true) aiRows = oSettings.aiDisplay;
    // use all rows
    else aiRows = oSettings.aiDisplayMaster; // all row numbers

    // set up data array   
    var asResultData = new Array();

    for (var i=0,c=aiRows.length; i<c; i++) {
        iRow = aiRows[i];
        var aData = this.fnGetData(iRow);
        var sValue = aData[iColumn];

        // ignore empty values?
        if (bIgnoreEmpty == true && sValue.length == 0) continue;

        // ignore unique values?
        else if (bUnique == true && jQuery.inArray(sValue, asResultData) > -1) continue;

        // else push the value onto the result data array
        else asResultData.push(sValue);
    }

    return asResultData;
}}(jQuery));

function fnCreateSelect(aData) 
{
    var r = '<select><option value=""></option>', i, iLen = aData.length;

    for (i = 0; i < iLen; i++) {
        // If string is a URL, handle it accordingly
        if (aData[i].indexOf("href") != -1) {
            var url = aData[i].substring(aData[i].indexOf('http'), aData[i].indexOf('">'));
            r += '<option title="' + url + '" value="' + url + '">' + url.substring(0, 25);
            if (url.length > 25)
                r += '...';
        }
        else {
            r += '<option title="' + aData[i] + '" value="' + aData[i] + '">' + aData[i].substring(0, 40)
            if (aData[i].length > 40)
                r += '...';
        }
        r += '</option>';
    }
    return r + '</select>';
}

/* Add a select menu for each TH element in the table footer */
$("tfoot td").each( function ( i ) {
    console.log('i: ' + i);

    if( i>0 && i<5 )
    {
        this.innerHTML = fnCreateSelect( oTable.fnGetColumnData(i) );
        $('select', this).change( function () {
            oTable.fnFilter( $(this).val(), i );
        });
    }
} );