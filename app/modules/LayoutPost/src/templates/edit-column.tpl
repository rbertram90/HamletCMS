<div class="ui modal" id="edit_column_form">
    <div class="header">Edit column</div>
    <div class="content">
        <form class="ui form">

            <div class="field">
                <label for="type">Content type</label>
                <select id="type" class="ui fluid dropdown">
                    <option></option>
                    <option value="text">Text</option>
                    <option value="image">Image</option>
                    <option value="code">Code</option>
                </select>
            </div>

            {* TEXT *}
            <div class="field">
                <label for="text_content">Text</label>
                <textarea id="text_content"></textarea>
            </div>

            <div class="field">
                <label for="background_colour">Background colour</label>
                <select id="background_colour">
                    <option value="">None</option>
                    <option value="red">Red</option>
                    <option value="orange">Orange</option>
                    <option value="yellow">Yellow</option>
                    <option value="olive">Olive</option>
                    <option value="green">Green</option>
                    <option value="teal">Teal</option>
                    <option value="blue">Blue</option>
                    <option value="violet">Violet</option>
                    <option value="purple">Purple</option>
                    <option value="pink">Pink</option>
                    <option value="brown">Brown</option>
                    <option value="grey">Grey</option>
                    <option value="black">Black</option>
                </select>
            </div>

            <div class="field">
                <label for="font_colour">Font colour</label>
                <input type="text" id="font_colour" placeholder="#000000">
            </div>
            {* END TEXT *}


            {* IMAGE *}
            <div class="field">
                <label>Select image:</label>
                {$imagesOutput}
                <input type="hidden" id="selected_image">
            </div>

            <div class="field">
                <label for="min_height">Minimum height</label>
                <input type="text" id="min_height" placeholder="auto">
            </div>
            {* END IMAGE *}


            {* CODE *}
            <div class="field">
                <label for="code_theme">Theme</label>
                <select id="code_theme" class="ui fluid dropdown">
                    <option value="ambiance">Ambiance</option>
                    <option value="chaos">Chaos</option>
                    <option value="clouds">Clouds</option>
                    <option value="clouds_midnight">Clouds midnight</option>
                    <option value="cobalt">Cobalt</option>
                    <option value="crimson_editor">Crimson</option>
                    <option value="dawn">Dawn</option>
                    <option value="dracula">Dracula</option>
                    <option value="dreamweaver">Dreamweaver</option>
                    <option value="eclipse">Eclipse</option>
                    <option value="gob">Gob</option>
                    <option value="gruvbox">Gruvbox</option>
                    <option value="idle_fingers">Idle fingers</option>
                    <option value="iplastic">iPlastic</option>
                    <option value="katzenmilch">Katzenmilch</option>
                    <option value="kr_theme">KR theme</option>
                    <option value="kurior">Kurior</option>
                    <option value="merbivore">Merbivore</option>
                    <option value="merbivore_soft">Merbivore soft</option>
                    <option value="mono_industrial">Mono industrial</option>
                    <option value="monokai">Monokai</option>
                    <option value="pastel_on_dark">Pastel on dark</option>
                    <option value="solarized_dark">Solarized dark</option>
                    <option value="solarized_light">Solarized light</option>
                    <option value="sqlserver">SQL Server</option>
                    <option value="terminal">Terminal</option>
                    <option value="textmate" selected>Textmate</option>
                    <option value="tomorrow">Tomorrow</option>
                    <option value="tomorrow_night">Tomorrow night</option>
                    <option value="tomorrow_night_blue">Tomorrow night blue</option>
                    <option value="tomorrow_night_bright">Tomorrow night bright</option>
                    <option value="tomorrow_night_eighties">Tomorrow night eighties</option>
                    <option value="twilight">Twilight</option>
                    <option value="vibrant_ink">Vibrant Ink</option>
                    <option value="xcode">X-code</option>
                </select>
            </div>

            <div class="field">
                <label for="code_lang">Language</label>
                <select id="code_lang" class="ui fluid dropdown">
                    <option value="abap">ABAP</option>
                    <option value="abc">abc</option>
                    <option value="actionscript">ActionScript</option>
                    <option value="ada">ADA</option>
                    <option value="apache_conf">Apache Conf</option>
                    <option value="apex">Apex (Salesforce)</option>
                    <option value="applescript">AppleScript</option>
                    <option value="asciidoc">AsciiDoc</option>
                    <option value="asl">ASL</option>
                    <option value="assembly_x86">x86 Assembly</option>
                    <option value="autohotkey">AutoHotkey</option>
                    <option value="batchfile">Batch</option>
                    <option value="bro">Bro</option>
                    <option value="c_cpp">C/C++</option>
                    <option value="cirru">Cirru</option>
                    <option value="clojure">Clojure</option>
                    <option value="cobol">COBOL</option>
                    <option value="coffee">CoffeeScript</option>
                    <option value="coldfusion">ColdFusion</option>
                    <option value="csharp">C#</option>
                    <option value="csound_document">cSounds document</option>
                    <option value="csound_orchestra">cSounds orchestra</option>
                    <option value="csound_score">cSounds score</option>
                    <option value="csp">CSP</option>
                    <option value="css">CSS</option>
                    <option value="curly">Curl</option>
                    <option value="d">D</option>
                    <option value="dart">Dart</option>
                    <option value="diff">Diff</option>
                    <option value="django">Django</option>
                    <option value="dockerfile">Dockerfile</option>
                    <option value="dot">Dot</option>
                    <option value="drools">Drools</option>
                    <option value="edifact">EDIFACT</option>
                    <option value="eiffel">Eiffel</option>
                    <option value="ejs">EJS</option>
                    <option value="elixir">Elixir</option>
                    <option value="elm">Elm</option>
                    <option value="erlang">Erlang</option>
                    <option value="forth">Forth</option>
                    <option value="fortran">Fortran</option>
                    <option value="fsharp">F#</option>
                    <option value="fsl">FSL</option>
                    <option value="gcode">G-code</option>
                    <option value="gherkin">Gherkin</option>
                    <option value="gitignore">Gitignore</option>
                    <option value="glsl">GLSL</option>
                    <option value="gobstones">Gobstones</option>
                    <option value="golang">Golang</option>
                    <option value="graphqlschema">GraphQL Schema</option>
                    <option value="groovy">Groovy</option>
                    <option value="haml">HAML</option>
                    <option value="handlebars">Handlebars</option>
                    <option value="haskell">Haskell</option>
                    <option value="haskell_cabal">Haskell cabal</option>
                    <option value="haxe">Haxe</option>
                    <option value="hjson">Hjson</option>
                    <option value="html_elixir">HTML elixir</option>
                    <option value="html_ruby">HTML ruby</option>
                    <option value="html">HTML</option>
                    <option value="ini">INI</option>
                    <option value="io">IO</option>
                    <option value="jack">Jack</option>
                    <option value="jade">Jade</option>
                    <option value="java">Java</option>
                    <option value="javascript" selected>JavaScript</option>
                    <option value="json">JSON</option>
                    <option value="jsoniq">JSONIQ</option>
                    <option value="jsp">JSP</option>
                    <option value="jssm">JSSM</option>
                    <option value="jsx">JSX</option>
                    <option value="julia">Julia</option>
                    <option value="kotlin">Kotlin</option>
                    <option value="latex">LaTeX</option>
                    <option value="less">Less</option>
                    <option value="liquid">Liquid</option>
                    <option value="lisp">Lisp</option>
                    <option value="livescript">Livescript</option>
                    <option value="logiql">Logiql</option>
                    <option value="logtalk">Logtalk</option>
                    <option value="lsl">LSL</option>
                    <option value="lua">Lua</option>
                    <option value="luapage">Luapage</option>
                    <option value="lucene">Lucene</option>
                    <option value="makefile">Makefile</option>
                    <option value="markdown">Markdown</option>
                    <option value="mask">Mask</option>
                    <option value="matlab">Matlab</option>
                    <option value="maze">Maze</option>
                    <option value="mel">Mel</option>
                    <option value="mixal">Mixal</option>
                    <option value="mushcode">Mushcode</option>
                    <option value="mysql">MySQL</option>
                    <option value="nix">Nix</option>
                    <option value="nsis">Nsis</option>
                    <option value="objectivec">Objectivec</option>
                    <option value="ocaml">Ocaml</option>
                    <option value="pascal">Pascal</option>
                    <option value="perl">Perl</option>
                    <option value="perl6">Perl6</option>
                    <option value="pgsql">Pgsql</option>
                    <option value="php_laravel_blade">PHP - Laravel blade</option>
                    <option value="php">PHP</option>
                    <option value="pig">Pig</option>
                    <option value="plain_text">Plain text</option>
                    <option value="powershell">Powershell</option>
                    <option value="praat">Praat</option>
                    <option value="prolog">Prolog</option>
                    <option value="properties">Properties</option>
                    <option value="protobuf">Protobuf</option>
                    <option value="puppet">Puppet</option>
                    <option value="python">Python</option>
                    <option value="r">R</option>
                    <option value="razor">Razor</option>
                    <option value="rdoc">Rdoc</option>
                    <option value="red">Red</option>
                    <option value="redshift">Redshift</option>
                    <option value="rhtml">RHTML</option>
                    <option value="rst">RST</option>
                    <option value="ruby">Ruby</option>
                    <option value="rust">Rust</option>
                    <option value="sass">SASS</option>
                    <option value="scad">SCAD</option>
                    <option value="scala">Scala</option>
                    <option value="scheme">scheme</option>
                    <option value="scss">SCSS</option>
                    <option value="sh">SH</option>
                    <option value="sjs">SJS</option>
                    <option value="slim">Slim</option>
                    <option value="smarty">Smarty</option>
                    <option value="snippets">Snippets</option>
                    <option value="soy_template">Soy template</option>
                    <option value="space">Space</option>
                    <option value="sparql">Sparql</option>
                    <option value="sql">SQL</option>
                    <option value="sqlserver">SQL Server</option>
                    <option value="stylus">Stylus</option>
                    <option value="svg">SVG</option>
                    <option value="swift">Swift</option>
                    <option value="tcl">TCL</option>
                    <option value="terraform">Terraform</option>
                    <option value="tex">TEX</option>
                    <option value="text">Text</option>
                    <option value="textile">Textile</option>
                    <option value="toml">TOML0</option>
                    <option value="tsx">TSX</option>
                    <option value="turtle">Turtle</option>
                    <option value="twig">Twig</option>
                    <option value="typescript">TypeScript</option>
                    <option value="vala">Vala</option>
                    <option value="vbscript">VBScript</option>
                    <option value="velocity">Velocity</option>
                    <option value="verilog">Verilog</option>
                    <option value="vhdl">VHDL</option>
                    <option value="visualforce">VisualForce</option>
                    <option value="wollok">Wollok</option>
                    <option value="xml">XML</option>
                    <option value="xquery">XQuery</option>
                    <option value="yaml">YAML</option>
                </select>
            </div>

            <div class="field">
                <label for="code_content">Code</label>
                <pre id="code_content" style="height: 300px; width: 100%">function foo(items) {
    var x = "All this is syntax highlighted";
    return x;
}</pre>
            </div>
            {* END CODE *}

            <input type="hidden" id="row_index" value="">
            <input type="hidden" id="column_index" value="">
        </form>
    </div>
    <div class="actions">
        <button class="ui teal approve button">Save</button>
        <button class="ui cancel button" type='button'>Cancel</button>
    </div>
</div>


<script>
var ace_editor = ace.edit("code_content");
ace_editor.setTheme("ace/theme/textmate");
ace_editor.session.setMode("ace/mode/javascript");
// editor.setReadOnly(true);  // false to make it editable

// Hide and show fields when field type is changed
$('#edit_column_form #type').change(function() {
    $('#edit_column_form .field').hide();
    $("#type").parent().show();

    switch ($(this).val()) {
        case 'text':
            $("#text_content").parent().show();
            $("#background_colour").parent().show();
            $("#font_colour").parent().show();
            break;

        case 'image':
            $("#selected_image").parent().show();
            $("#min_height").parent().show();
            break;

        case 'code':
            $("#code_content").parent().show();
            $("#code_lang").parent().show();
            $("#code_theme").parent().show();
            $("#background_colour").parent().show();
            break;
    }
});

$('#edit_column_form #code_theme').change(function() {
    ace_editor.setTheme('ace/theme/' + $("#code_theme").val());
});

$('#edit_column_form #code_lang').change(function() {
    ace_editor.session.setMode('ace/mode/' + $("#code_lang").val());
});

</script>