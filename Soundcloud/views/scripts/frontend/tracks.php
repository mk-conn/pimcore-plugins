<?php if($this->message) : ?>
<div class="error"> <?php print $this->message ?></div>
<?php endif; ?>
<?php if($this->tracks):?>
	<?php /* @var $track Soundcloud_Track */ ?>
	<?php foreach($this->tracks as $track) : ?>
	<div class="item">		
		<div class="track">	
			<object height="<?php print $this->playerHeight ?>" width="<?php print $this->playerWidth ?>" id="track_<?php print $track->getId() ?>">
				<param 
					name="movie" 
					value="http://player.soundcloud.com/player.swf?url=<?php print $this->escape($track->getUri()) ?><?php print $track->getOptions()?>" />
				<param name="allowscriptaccess" value="always" />
				<embed 
					allowscriptaccess="always" 
					height="<?php print $this->playerHeight ?>"
					src="http://player.soundcloud.com/player.swf?url=<?php print $this->escape($track->getUri()) ?><?php print $track->getOptions()?>"
					type="application/x-shockwave-flash" 
					width="<?php print $this->playerWidth ?>" 
					/>
			</object>
			<span>
				<a href="<?php print $this->escape($track->getTrackPermalink()) ?>">
					<?php print $track->getTitle() ?>
				</a>
				by
				<a href="<?php print $this->escape($track->getUserPermalink()) ?>">
					<?php print $track->getUser() ?>
				</a>
			</span>
		</div>
	</div>
	<?php endforeach;?>
<?php endif;?>