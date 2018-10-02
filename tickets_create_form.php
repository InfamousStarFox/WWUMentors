<form action="includes/pages/tickets_create.php" method="POST">
	<div class="form-group">
		<label for="class">CSCI Class #</label>
		<input type="number" class="form-control" name="class" value="141">
	</div>
	<div class="form-group">
		<label for="location">Location</label>
		<input type="text" class="form-control" name="location" placeholder="Near the printer">
	</div>
	<div class="form-group">
		<label for="question">Question</label>
		<input type="text" class="form-control" name="question" placeholder="How do I make a for loop?" required>
	</div>
	<div class="form-group">
		<label for="question">Difficulty</label>
		<select class="form-control" name="difficulty">
			<option value="" selected>Select</option>
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
		</select>
	</div>
	
	<br>
	<input type="submit" value="Submit" class="btn btn-primary">
</form>