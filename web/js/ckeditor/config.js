CKEDITOR.editorConfig = function( config )
{
	config.entities = false;
	config.htmlEncodeOutput = true;	
	config.allowedContent = true;
	config.protectedSource.push( /<object[\s|\S]+?<\/object>/gi );
}