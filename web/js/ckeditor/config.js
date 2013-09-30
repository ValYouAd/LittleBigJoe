CKEDITOR.stylesSet.add('myStyles',
[
   { name : 'ck_embed_container', element : 'div', attributes : { 'class' : 'ck_embed_container'} },
   { name : 'ck_object_container', element : 'div', attributes : { 'class' : 'ck_object_container'} }
]);

CKEDITOR.editorConfig = function( config )
{
	config.entities = false;
	config.htmlEncodeOutput = true;
	config.protectedSource.push( /<div[\s|\S]+?<\/div>/gi );
	config.protectedSource.push( /<iframe[\s|\S]+?<\/iframe>/gi );
	config.protectedSource.push( /<object[\s|\S]+?<\/object>/gi );
	config.oembed_WrapperClass = 'ck_embed_container';
	config.stylesSet = 'myStyles';
}