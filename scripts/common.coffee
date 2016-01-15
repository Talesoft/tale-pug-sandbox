(($) ->

    $ ->

        ace.config.set 'packaged', true
        ace.config.set 'basePath', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.0'

        jadeEditor = ace.edit 'jadeEditor'
        phpEditor = ace.edit 'phpEditor'
        phpEditorEl = document.getElementById 'phpEditor'

        jadeEditor.setTheme 'ace/theme/xcode'
        jadeEditor.getSession().setMode 'ace/mode/jade'
        jadeEditor.getSession().setUseWorker false
        jadeEditor.getSession().setUseSoftTabs true
        jadeEditor.$blockScrolling = Infinity
        jadeEditor.setValue `!{json_encode($jade)}`
        jadeEditor.navigateFileStart()
        phpEditor.setTheme 'ace/theme/xcode'
        phpEditor.getSession().setMode 'ace/mode/php'
        phpEditor.getSession().setUseWorker false
        phpEditor.getSession().setUseSoftTabs true
        phpEditor.$blockScrolling = Infinity
        phpEditor.setReadOnly true

        optionsForm = document.getElementById 'optionsForm'
        saveButton = document.getElementById 'saveButton'

        iv = null


        save = ->

            args =
                jade: jadeEditor.getValue()
                mode: 'save'

            $.post 'index.php', args, (result) ->

                unless result.success

                    window.alert 'Failed saving Jade. CTRL+C the Jade code and reload the page.'
                    return

                window.location.href = 'id-' + result.id + '.html'

        compile = ->

            phpEditorEl.classList.add 'compiling'
            args = $(optionsForm).serialize() + '&jade=' + encodeURIComponent(jadeEditor.getValue())

            $.post 'index.php', args, (result) ->

                resultText = if result.success then result.output else result.message

                phpEditorEl.classList.remove 'compiling'
                phpEditor.setValue resultText;
                phpEditor.navigateFileStart();

        changed = ->

            if iv
                window.clearTimeout iv

            phpEditorEl.classList.add 'compiling'
            iv = window.setTimeout compile, 50

        jadeEditor.getSession().on 'change', changed
        $('input', optionsForm).change compile
        $(saveButton).click (e) ->

            e.preventDefault()
            save()

        compile();
) jQuery