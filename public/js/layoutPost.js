var LayoutEditor = function(blogID) {
    this.blogID = blogID;
    this.definition = null;
    this.outputElement = null;
    this.jsonElement = null;
    this.defaultDefinition = {
        'rows': [
            {
                'columnLayout': 'singleColumn',
                'columns': [
                    {
                        'contentType': 'text',
                        'textContent': 'Your content here...'
                    }
                ]
            }
        ]
    };

    window.layouteditor = this;

    document.querySelector('#edit_column_form .approve.button').addEventListener("click", this.saveColumnData);
    document.querySelector('#edit_row_form .approve.button').addEventListener("click", this.saveRowData);
};

LayoutEditor.prototype.loadJSON = function() {
    if (this.jsonElement.value.length > 0) {
        this.definition = JSON.parse(this.jsonElement.value);
    }
    else {
        this.definition = this.defaultDefinition;
    }
    console.log(this.definition);
};

LayoutEditor.prototype.setOutputElement = function (elem) {
    elem.classList.add("ui");
    elem.classList.add("grid");
    elem.classList.add("layouteditor");
    this.outputElement = elem;
};

LayoutEditor.prototype.setJSONElement = function (elem) {
    this.jsonElement = elem;
};

LayoutEditor.prototype.generateHTML = function() {
    var out = "";

    if (this.definition == null) {
        this.loadJSON();
    }

    for (var r = 0; r < this.definition.rows.length; r++) {
        var rowClasses = "";
        var row = this.definition.rows[r];
        var rOut = "";
        var columnLayout = row.columnLayout;
/*
        <option value="twoColumns_75">2 Columns: 75% | 25%</option>
        <option value="twoColumns_25">2 Columns: 25% | 75%</option>
        <option value="twoColumns_66">2 Columns: 66% | 33%</option>
        <option value="twoColumns_33">2 Columns: 33% | 66%</option>
*/
        columnWidths = null;

        rOut += "<a class='move_row_up' class='ui compact icon button'><i class='arrow up icon'></i></a>";
        rOut += "<a class='move_row_down' class='ui compact icon button'><i class='arrow down icon'></i></a>";

        switch (columnLayout) {
            case "twoColumns_50":
                rowClasses = "two column";
                break;

            case "twoColumns_75":
                columnWidths = [75, 25];
                break;

            case "twoColumns_75":
                columnWidths = [25, 75];
                break;

            case "twoColumns_66":
                columnWidths = [66, 33];
                break;

            case "twoColumns_66":
                columnWidths = [33, 66];
                break;

            case "threeColumns":
                rowClasses = "three column";
                break;

            case "fourColumns":
                rowClasses = "four column";
                break;

            default:
            case "singleColumn":
                columnWidths = [100];
                break;
        }

        for (var c = 0; c < row.columns.length; c++) {

            var column = row.columns[c];
            var classes = "";
            if (columnWidths) {
                switch (columnWidths[c]) {
                    case 100: classes = "sixteen wide"; break;
                    case 75: classes = "twelve wide"; break;
                    case 66: classes = "ten wide"; break;
                    case 33: classes = "six wide"; break;
                    case 25: classes = "four wide"; break;
                }
            }

            rOut += "<div class='" + classes + " column'><div class='column-inner' data-column-index='" + c + "' data-row-index='" + r + "'>"

            if (column.textContent) {
                rOut += column.textContent.replace(/(?:\r\n|\r|\n)/g, '<br>');;
            }
            if (column.image) {
                rOut += '<img src="/blogdata/' + this.blogID + '/images/' + column.image + '" alt="' + column.image + '">';
            }
            if (column.codeContent) {
                rOut += column.codeLanguage;
            }

            rOut += "</div></div>"
        }

        out += "<div class='" + rowClasses + " row' data-row-index='" + r + "'>" + rOut + "</div>";
    }

    out += "<button class='ui button' id='add_row'>Add row</button>"

    this.outputElement.innerHTML = out;

    // Event listeners

    var cols = document.querySelectorAll(".layouteditor .column-inner");
    for (var c = 0; c < cols.length; c++) {
        var col = cols[c];

        col.addEventListener("click", this.showEditColumnModal);
    }
    
    var rows = document.querySelectorAll(".layouteditor .row");
    for (var r = 0; r < rows.length; r++) {
        var row = rows[r];

        row.addEventListener("click", this.showEditRowModal);
    }

    document.querySelector('#add_row').addEventListener("click", this.addRow);

    $('.move_row_up').click(this.moveRowUp);
    $('.move_row_down').click(this.moveRowDown);

    // Re-generate JSON
    this.jsonElement.innerHTML = JSON.stringify(this.definition, null, 4);
    $(this.jsonElement).trigger('change');
};

LayoutEditor.prototype.showEditColumnModal = function(event) {
    var rowIndex = this.dataset.rowIndex;
    var columnIndex = this.dataset.columnIndex;
    var definition = window.layouteditor.definition.rows[rowIndex].columns[columnIndex];

    var modal = $('#edit_column_form');
    modal.find('.field').hide();
    modal.find("#type").parent().show();

    switch (definition.contentType) {
        case 'text':
            modal.find("#text_content").parent().show();
            modal.find("#background_colour").parent().show();
            modal.find("#font_colour").parent().show();

            modal.find('#text_content').val(definition.textContent);
            modal.find('#background_colour').val(definition.backgroundColour);
            modal.find('#font_colour').val(definition.fontColour);
            break;

        case 'image':
            modal.find("#selected_image")
                .val(definition.image)
                .parent()
                .show();

            modal.find("#min_height")
                .val(definition.minimumHeight)
                .parent()
                .show();

            modal.find('.selectableimage[data-name="' + definition.image + '"]').css('border', '3px solid #0c0');
            break;

        case 'code':
            modal.find("#code_content")
                .parent()
                .show();
            ace_editor.setValue(definition.codeContent);

            modal.find("#code_theme")
                .val(definition.codeTheme)
                .trigger('change')
                .parent()
                .show();
            modal.find("#code_lang")
                .val(definition.codeLanguage)
                .trigger('change')
                .parent()
                .show();
            break;
    }

    modal.find('#type').val(definition.contentType).parent().show();

    modal.find('#row_index').val(rowIndex);
    modal.find('#column_index').val(columnIndex);
    modal.modal('show');

    event.stopPropagation();
};

LayoutEditor.prototype.saveColumnData = function(event) {

    var form = $('#edit_column_form');
    var rowIndex = form.find('#row_index').val();
    var columnIndex = form.find('#column_index').val();

    switch (form.find('#type').val()) {
        case 'text': 
            window.layouteditor.definition.rows[rowIndex].columns[columnIndex] = {
                'contentType': form.find('#type').val(),
                'textContent': form.find('#text_content').val(),
                'backgroundColour': form.find('#background_colour').val(),
                'fontColour': form.find('#font_colour').val()
            };
            break;

        case 'image':
            window.layouteditor.definition.rows[rowIndex].columns[columnIndex] = {
                'contentType': form.find('#type').val(),
                'image': form.find('#selected_image').val(),
                'minimumHeight': form.find('#min_height').val()
            };
            break;

        case 'code':
            window.layouteditor.definition.rows[rowIndex].columns[columnIndex] = {
                'contentType': form.find('#type').val(),
                'codeContent': ace_editor.getValue(),
                'codeLanguage': form.find('#code_lang').val(),
                'codeTheme': form.find('#code_theme').val()
            };
            break;
    }


    window.layouteditor.generateHTML();
};


LayoutEditor.prototype.showEditRowModal = function(event) {
    var rowIndex = this.dataset.rowIndex;
    var definition = window.layouteditor.definition.rows[rowIndex];

    var modal = $('#edit_row_form');
    modal.find('#columnlayout').val(definition.columnLayout);
    modal.find('#row_index').val(rowIndex);
    modal.modal('show');

    event.stopPropagation();
};

LayoutEditor.prototype.saveRowData = function(event) {
    var form = $('#edit_row_form');
    var rowIndex = form.find('#row_index').val();
    var columnLayout = form.find('#columnlayout').val();
    var columnCount = 2;

    switch (columnLayout) {
        case "fourColumns":
            columnCount = 4;
            break;
        case "threeColumns":
            columnCount = 3;
            break;
        case "singleColumn":
            columnCount = 1;
            break;
    }

    var currentColumns = window.layouteditor.definition.rows[rowIndex].columns;
    if (currentColumns.length < columnCount) {
        for (var c = currentColumns.length; c < columnCount; c++) {
            window.layouteditor.definition.rows[rowIndex].columns.push({
                'contentType': 'text',
                'textContent': 'New column'
            });
        }
    }
    else if (currentColumns.length > columnCount) {
        for (var c = currentColumns.length; c > columnCount; c--) {
            window.layouteditor.definition.rows[rowIndex].columns.pop();
        }
    }
    
    window.layouteditor.definition.rows[rowIndex] = {
        'columnLayout': columnLayout,
        'columns': window.layouteditor.definition.rows[rowIndex].columns
    };

    window.layouteditor.generateHTML();
};

LayoutEditor.prototype.addRow = function() {
    window.layouteditor.definition.rows.push({
        'layout': 'singleColumn',
        'columns': [
            {
                'contentType': 'text',
                'textContent': 'New column'
            }
        ]
    });

    window.layouteditor.generateHTML();
};

LayoutEditor.prototype.moveRowUp = function(event) {
    var rowIndex = $(this).parent().data('rowIndex');

    if (rowIndex > 0) {
        var temp = window.layouteditor.definition.rows[rowIndex];
        window.layouteditor.definition.rows[rowIndex] = window.layouteditor.definition.rows[rowIndex - 1];
        window.layouteditor.definition.rows[rowIndex - 1] = temp;
        window.layouteditor.generateHTML();
    }

    event.preventDefault();
    event.stopPropagation();
};

LayoutEditor.prototype.moveRowDown = function(event) {
    var rowIndex = $(this).parent().data('rowIndex');

    if (rowIndex < window.layouteditor.definition.rows.length - 1) {
        var temp = window.layouteditor.definition.rows[rowIndex];
        window.layouteditor.definition.rows[rowIndex] = window.layouteditor.definition.rows[rowIndex + 1];
        window.layouteditor.definition.rows[rowIndex + 1] = temp;
        window.layouteditor.generateHTML();
    }

    event.preventDefault();
    event.stopPropagation();
};