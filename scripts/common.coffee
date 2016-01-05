(($) ->

    $ ->

        ace.config.set "packaged", true
        ace.config.set "basePath", 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.0'

        jadeEditor = ace.edit 'jadeEditor'
        phpEditor = ace.edit 'phpEditor'
        phpEditorEl = document.getElementById 'phpEditor'

        jadeEditor.setTheme 'ace/theme/xcode'
        jadeEditor.getSession().setMode 'ace/mode/jade'
        jadeEditor.getSession().setUseWorker false
        jadeEditor.getSession().setUseSoftTabs true
        jadeEditor.$blockScrolling = Infinity
        jadeEditor.setValue `!{$exampleCode}`
        jadeEditor.navigateFileStart()
        phpEditor.setTheme 'ace/theme/xcode'
        phpEditor.getSession().setMode 'ace/mode/php'
        phpEditor.getSession().setUseWorker false
        phpEditor.getSession().setUseSoftTabs true
        phpEditor.$blockScrolling = Infinity
        phpEditor.setReadOnly true

        prettyCheckbox = document.getElementById 'prettyCheckbox'
        standAloneCheckbox = document.getElementById 'standAloneCheckbox'

        compile = ->

            phpEditorEl.classList.add 'compiling'
            args =
                jade: jadeEditor.getValue()
                pretty: if prettyCheckbox.checked then 'true' else 'false'
                standAlone: if standAloneCheckbox.checked then 'true' else 'false'

            $.post 'index.php', args, (result) ->

                phpEditorEl.classList.remove 'compiling'
                phpEditor.setValue result;
                phpEditor.navigateFileStart();

        iv = null
        changed = ->

            if iv
                window.clearTimeout iv

            phpEditorEl.classList.add 'compiling'
            iv = window.setTimeout compile, 50

        jadeEditor.getSession().on 'change', changed
        $([prettyCheckbox, standAloneCheckbox]).change changed
        compile();
) jQuery