<div class="jet-progress-bar__inner">
	<div class="jet-progress-bar__title"><?php
		$this->_icon( 'icon', '<span class="jet-progress-bar__title-icon jet-elements-icon">%s</span>' );
		$this->_html( 'title', '<span class="jet-progress-bar__title-text">%s</span>' );?></div>
	<div class="jet-progress-bar__wrapper">
		<div class="jet-progress-bar__status-bar"></div>
		<div class="jet-progress-bar__percent"><?php echo $percent_html ?></div>
	</div>
</div>
