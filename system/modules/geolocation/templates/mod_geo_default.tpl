
<!-- indexer::stop -->
<?php echo $this->strJS; ?>
<div class="<?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>
<?php if ($this->geo_close): ?>

<p class="geo_close"><?php echo $GLOBALS['TL_LANG']['MSC']['collapseNode']; ?></p>
<?php endif; ?>
<?php if ($this->headline): ?>

<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
<?php endif; ?>

<p id="geoInfo_<?php echo $this->strId; ?>">
<?php if ($this->UserGeolocation->isTracked()): ?>
<?php echo (!$this->UserGeolocation->isFailed()) ? $GLOBALS['TL_LANG']['GEO']['your_country'].' '.$this->UserGeolocation->getCountry() : $GLOBALS['TL_LANG']['GEO']['unknown_country']; ?>
<?php else: ?>&nbsp;
<?php endif; ?></p>

<?php if ($this->UserGeolocation->getTrackType() == GeolocationContainer::LOCATION_BY_USER): ?>
<div>
	<select id="geoChange_<?php echo $this->strId; ?>" class="geochange" >
	<?php foreach ($this->getCountries() as $key => $value) : ?>
		<option value="<?php echo $key; ?>"<?php if ($key == $this->UserGeolocation->getCountryShort()) echo ' selected="selected"'; ?>><?php echo $value; ?></option>
	<?php endforeach; ?>
	</select>
	<input type="button" value="Senden" name="changeCountry" onclick="GeoUpdater.changeGeoLocation('geoChange_<?php echo $this->strId; ?>', 'geoInfo_<?php echo $this->strId; ?>','<?php echo $this->lang; ?>');" />
</div>
<?php endif; ?>

</div>
<!-- indexer::continue -->