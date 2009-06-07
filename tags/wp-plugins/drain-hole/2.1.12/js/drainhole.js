function delete_hole (item)
{
	if (confirm (wp_dh_deletehole))
	{
	  new Ajax.Request (wp_base + '?cmd=delete_hole&id=' + item,
	    {
	      asynchronous: true,
	      onLoading: function(request) { Element.show ('loading')},
	      onComplete: function(request) { Element.hide ('loading'); Element.remove ('hole_' + item);}
	    });
	}
	
	return false;
}

function edit_hole (item)
{
  Modalbox.show (wp_base + '?id=' + item + '&cmd=edit_hole&id=' + item,
  {
    title: 'Edit hole',
    overlayOpacity: 0.4,
    inactiveFade: false,
    afterLoad:function ()
    {
    }
  });

  return false;
}


function save_hole (item,form)
{
//  Modalbox.deactivate ();
  Modalbox.show (wp_base + '?id=' + item + '&cmd=save_hole', { title: 'Saving', overlayOpacity: 0.4, inactiveFade: false, method: 'post', params: Form.serialize (form), afterLoad:function()
    {
      Modalbox.hide ();
      new Ajax.Updater ('hole_' + item, wp_base + '?cmd=show_hole&id=' + item, { asynchronous: true });
    }});

  return false;
}



function delete_file (item)
{
	if (confirm ('Are you sure you want to delete this file?'))
	{
	  new Ajax.Request (wp_base + '?cmd=delete_file&id=' + item,
	    {
	      asynchronous: true,
	      onLoading: function(request) { Element.show ('loading')},
	      onComplete: function(request) { Element.hide ('loading'); Element.remove ('file_' + item);}
	    });
	}
}


function delete_stat (item)
{
  new Ajax.Request (wp_base + '?cmd=delete_stat&id=' + item,
    {
      asynchronous: true,
      onLoading: function(request) { Element.show ('loading')},
      onComplete: function(request) { Element.hide ('loading'); Element.remove ('stat_' + item);}
    });
}


function print_chart ()
{
  document.charts.SetVariable ( 'print_chart', true );
  return false;
}




function new_version (item)
{
  Modalbox.show (wp_base + '?id=' + item + '&cmd=new_version', { title: 'New version branch', overlayOpacity: 0.4, inactiveFade: false, afterLoad:function ()
    {
      $('newversion').focus ();
    }});
  return false;
}

function save_new_version (item,form)
{
//  Modalbox.deactivate ();
  Modalbox.show (wp_base + '?id=' + item + '&cmd=save_new_version', { title: 'Saving', overlayOpacity: 0.4, inactiveFade: false, method: 'post', params: Form.serialize (form), afterLoad:function()
    {
      Modalbox.hide ();
      new Ajax.Updater ('file_' + item, wp_base + '?cmd=show_file&id=' + item, { asynchronous: true });
    }});

  return false;
}


function edit_file (item)
{
  Modalbox.show (wp_base + '?id=' + item + '&cmd=edit_file&id=' + item,
  {
    title: 'Edit file',
    overlayOpacity: 0.4,
    inactiveFade: false,
    afterLoad:function ()
    {
    }
  });

  return false;
}

function save_file (item,form)
{
//  Modalbox.deactivate ();
  Modalbox.show (wp_base + '?id=' + item + '&cmd=save_file&id=' + item,
  {
    title: 'Saving',
    overlayOpacity: 0.4,
    inactiveFade: false,
    method: 'post',
    params: Form.serialize (form),
    afterLoad:function()
    {
      Modalbox.hide ();
      new Ajax.Updater ('file_' + item, wp_base + '?cmd=show_file&id=' + item, { asynchronous: true });
    }
  });

  return false;
}


function edit_version (item)
{
  Modalbox.show (wp_base + '?id=' + item + '&cmd=edit_version&id=' + item,
  {
    title: 'Edit version',
    overlayOpacity: 0.4,
    inactiveFade: false,
    afterLoad:function ()
    {
    }
  });

  return false;
}

function save_version (item,form)
{
//  Modalbox.deactivate ();
  Modalbox.show (wp_base + '?id=' + item + '&cmd=save_version&id=' + item,
  {
    title: 'Saving',
    overlayOpacity: 0.4,
    inactiveFade: false,
    method: 'post',
    params: Form.serialize (form),
    afterLoad:function()
    {
      Modalbox.hide ();
      new Ajax.Updater ('version_' + item, wp_base + '?cmd=show_version&id=' + item, { asynchronous: true });
    }
  });

  return false;
}

function delete_version (item)
{
	if (confirm (wp_dh_deleteversion))
	{
	  new Ajax.Request (wp_base + '?cmd=delete_version&id=' + item,
	    {
	      asynchronous: true,
	      onComplete: function(request) { Element.remove ('version_' + item);}
	    });
	}
	
	return false;
}

function update_dir_warning (text)
{
	if (text.startsWith (wp_dh_base_home) == true && $('error_dir').style.display == 'none')
		Element.show ('error_dir');
	else if (text.startsWith (wp_dh_base_home) == false && $('error_dir').style.display != 'none')
		Element.hide ('error_dir');	
}

function dirKey (event)
{
	var element = '/' + event.element ().value.replace (/^\/*/, '').replace (/\/*$/, '') + '/';
	
	update_dir_warning (element);
		
	$('base_dir').innerHTML = element.stripTags ();
}

function update_url_warning (text)
{
	if (text.startsWith (wp_dh_home_url) == false && $('error_url').style.display == 'none')
		Element.show ('error_url');
	else if (text.startsWith (wp_dh_home_url) == true && $('error_url').style.display != 'none')
		Element.hide ('error_url');
}

function urlKey (event)
{
	var element = '/' + event.element ().value.replace (/^\/*/, '').replace (/\/*$/, '');

	update_url_warning (element);
		
	$('base_url').innerHTML = escape (element);
}
