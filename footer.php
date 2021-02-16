<div id="sqlModal" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">SQL Queries</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p>
					<?php
					if (hasRole($role, ['admin'])) {
						if (isset($sql)) {
							echo htmlspecialchars($sql) . '<br><br>';
						}
						if (isset($sql2)) {
							echo $sql2 . '<br><br>';
						}
						if (isset($sql3)) {
							echo $sql3 . '<br><br>';
						}
						if (isset($sql4)) {
							echo $sql4 . '<br><br>';
						}
						if (isset($sql5)) {
							echo $sql5 . '<br><br>';
						}
						if (isset($tsql)) {
							echo $tsql . '<br><br>';
						}
						if (isset($tsql1)) {
							echo $tsql1 . '<br><br>';
						}
						if (isset($tsql2)) {
							echo $tsql2 . '<br><br>';
						}
						if (isset($tsql3)) {
							echo $tsql3 . '<br><br>';
						}
						if (isset($tsql4)) {
							echo $tsql4 . '<br><br>';
						}
					}
					?>
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<footer class="footer noPrint">
	<div class="container" style="display: flex; justify-content: space-between;">
		<?php if (hasRole($role, ['admin'])) { ?>
			<button type="button" class="btn btn-light" data-toggle="modal" data-target="#sqlModal">❓</button>
		<?php } else { ?>
			<div></div>
		<?php } ?>
		<p>Contact <a href="mailto:softwaresupport@signpost.eu">Software Support</a> for issues 😊</p>
	</div>
</footer>
<script src="lijstControl.js"></script>
</body>