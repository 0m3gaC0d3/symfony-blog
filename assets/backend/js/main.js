require('../css/main.less')

var $ = require('jquery')
var TagsInput = require('./tagsinput')($)
require('@fancyapps/fancybox')
require('./fancybox_setup')($)
var tinymce = require('tinymce/tinymce')
require('tinymce/themes/modern/theme')
require('tinymce/plugins/paste')
require('tinymce/plugins/link')
require('./tinymce_init')(tinymce)
if (TagsInput !== undefined) {
  TagsInput.refresh()
}

