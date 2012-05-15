function add_author_edit_links( row ) {
	var reg = ameAjaxL10n.postType + "-([0-9]+)";
	var Ausdruck = new RegExp(reg, "i");
	var post_id = Ausdruck.exec(row.id)[1];
	jQuery("td.column-author a[href*='edit.php?post_type='], td.column-author a[href*='edit.php?author='], td.column-author a[href*='edit-pages.php?author=']", row).each(function() {
		jQuery(this).after(' <a href="javascript:void(0);" onclick="ame_author_edit(' + post_id + ', \'' + ameAjaxL10n.postType + '\');"><img src="' + ameAjaxL10n.imgUrl + 'edit_small.gif" border="0" alt="' + ameAjaxL10n.Edit + '" title="' + ameAjaxL10n.Edit + '" style="float:none;" /></a>');
	});
}

function ame_roll_through_author_rows() {
	jQuery(".widefat tr[id^='" + ameAjaxL10n.postType + "-']").each(function() {
    	add_author_edit_links(this);
  	});
}


function add_title_edit_links( row ) {
	var reg = ameAjaxL10n.postType + "-([0-9]+)";
	var Ausdruck = new RegExp(reg, "i");
	var post_id = Ausdruck.exec(row.id)[1];
	jQuery("a[href*='post.php?post='].row-title, a[href*='post.php?action=edit&post='].row-title, a[href*='page.php?action=edit&post='].row-title", row).each(function() {
		var title = jQuery(this).html();
		title = title.replace(/— /g, "");
		jQuery(this).after(' <a href="javascript:void(0);" onclick="ame_title_edit(' + post_id + ', \'' + title + '\', \'' + ameAjaxL10n.postType + '\');"><img src="' + ameAjaxL10n.imgUrl + 'edit_small.gif" border="0" alt="' + ameAjaxL10n.Edit + '" title="' + ameAjaxL10n.Edit + '" /></a>');
	});
}

function ame_roll_through_title_rows() {
	jQuery(".widefat tr[id^='" + ameAjaxL10n.postType + "-']").each(function() {
		add_title_edit_links( this );
	});
}


function add_revision_links( row ) {
	var reg = ameAjaxL10n.postType + "-([0-9]+)";
	var Ausdruck = new RegExp(reg, "i");
	var post_id = Ausdruck.exec(row.id)[1];
	jQuery("a[href*='post.php?post='].row-title, a[href*='post.php?action=edit&post='].row-title, a[href*='page.php?action=edit&post='].row-title", row).each(function() {
		if ( jQuery(".widefat tr[id='" + row.id + "'] input[name^='amehasrev']").length > 0 ) {
			jQuery(this).parent().parent().find('span.edit').prepend('<a class="thickbox" href="#TB_inline?height=165&amp;width=300&amp;inlineId=amerevisionwrap' + post_id + '" title="' + ameAjaxL10n.Revisions + '" style="font-weight:200;">' + ameAjaxL10n.Revisions + '</a> | ');
			re_init();
		}
	});
}

function ame_roll_through_revision_rows() {
	jQuery(".widefat tr[id^='" + ameAjaxL10n.postType + "-']").each(function() {
		add_revision_links(this);
  	});
}