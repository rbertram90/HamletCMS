<div class="ui modal" id="edit_row_form">
    <div class="header">Edit row</div>
    <div class="content">
        <div class="ui message"><strong>Important</strong> reducing the number of columns will result in data being lost from the last column(s). Please ensure you have saved this content before continuing.</div>
        <form class="ui form">
            <div class="field">
                <label for="columnlayout">Layout</label>
                <select id="columnlayout" class="ui fluid dropdown">
                    <option value="singleColumn">Single column</option>
                    <option value="twoColumns_50">2 Columns: Equal widths</option>
                    <option value="twoColumns_75">2 Columns: 75% | 25%</option>
                    <option value="twoColumns_25">2 Columns: 25% | 75%</option>
                    <option value="twoColumns_66">2 Columns: 66% | 33%</option>
                    <option value="twoColumns_33">2 Columns: 33% | 66%</option>
                    <option value="threeColumns">3 Columns: Equal widths</option>
                    <option value="fourColumns">4 Columns: Equal widths</option>
                </select>
            </div>

            <input type="hidden" id="row_index" value="">
        </form>
    </div>
    <div class="actions">
        <button class="ui teal approve button">Save</button>
        <button class="ui cancel button" type='button'>Cancel</button>
    </div>
</div>