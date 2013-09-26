CKEDITOR.editorConfig = function( config )
{
	config.entities = false;
	config.htmlEncodeOutput = true;
	config.protectedSource.push( /<iframe[\s|\S]+?<\/iframe>/gi );
	config.protectedSource.push( /<object[\s|\S]+?<\/object>/gi );
}