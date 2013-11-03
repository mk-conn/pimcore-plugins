<?php $this->headStyle()->captureStart('PREPEND'); ?>
.dropbox-container {
	margin: 0 0 0 43px;
}

a.soundcloud-dropbox:hover {
	color: white !important;
	background-color: transparent !important;
	background-position: -250px 0 !important;
}

*html a.soundcloud-dropbox {
	background-image: none !important;
	filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='(http://a1.soundcloud.com/images/dropbox_small_dark.png?3e8e24)', sizingMethod='crop') !important;
}

a.soundcloud-dropbox {
	display: block;
	background: transparent url(http://a1.soundcloud.com/images/dropbox_small_dark.png?3e8e24) top left no-repeat;
	color: #D9D9D9;
	font-size: 10px;
	height: 30px;
	padding: 26px 60px 0 12px;
	width: 127px;
	text-decoration: none;
	font-family: "Lucida Grande", Helvetica, Arial, sans-serif;
	line-height: 1.3em
}
<?php $this->headStyle()->captureEnd(); ?>
<div class="dropbox-container">
	<a href="http://soundcloud.com/<?php print $this->username ?>/dropbox"
	   target="_blank" title="<?php print $this->title ?>"
	   class="soundcloud-dropbox"><?php print $this->title ?></a>
</div>