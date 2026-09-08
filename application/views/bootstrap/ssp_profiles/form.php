<script src="/assets/bootstrap/js/jquery.validate.min.js"></script>
<?php if ($lang == 'germany') : ?>
	<script src="/assets/js/validation/messages_de.js"></script>
<?php endif ?>
<?php
$this->load->view("bootstrap/players/player_map");
?>

<style>
	.select2-selection__rendered {
		overflow: auto;
		max-height: 200px !important;
	}

	#hourGrid td,
	#hourGrid th {
		padding: 2px 3px;
		vertical-align: middle;
		white-space: nowrap;
	}

	#hourGrid input.hour-input {
		width: 46px;
		padding: 2px 4px;
		text-align: center;
		display: inline-block;
	}

	#hourGrid .sum-cell {
		font-weight: bold;
		min-width: 46px;
		text-align: center;
		display: inline-block;
	}

	#hourGrid .sum-bad {
		color: #d63939;
		background-color: rgba(214, 57, 57, .1);
		border-radius: 3px;
	}

	/* keep the server name column visible while scrolling the wide grid */
	#hourGrid th:first-child,
	#hourGrid td:first-child {
		position: sticky;
		left: 0;
		background: #ffffff;
		z-index: 1;
	}

	.rule-card {
		border: 1px solid #e6e7e9;
		border-left: 3px solid #206bc4;
		border-radius: 4px;
		background: #fafbfc;
		padding: 12px 12px 4px;
		margin-bottom: 12px;
	}

	.rule-head {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 8px;
	}

	.rule-head .rule-title {
		font-weight: 600;
		color: #49566c;
	}
</style>

<div class="row">
	<div class="col-12 m-auto pt-3 pb-2 mb-3">
		<div class="card">
			<div class="card-header">
				<h2><?php echo $title ?></h2>
			</div>
			<form id="profileForm">
				<div class="card-body">
					<div id="validateTips"></div>
					<div class="row g-3">

						<div class="col-12">
							<label for="profile_name"><?php echo lang('name'); ?></label>
							<input type="text" class="form-control" id="profile_name" required value="<?php echo htmlspecialchars($profile_name); ?>" />
							<input type="hidden" id="old_name" value="<?php echo htmlspecialchars($profile_name); ?>" />
						</div>

						<div class="col-12">
							<label for="server-select-options"><?php echo lang('ssp.servers'); ?></label>
							<select id="server-select-options" class="form-select select2" multiple></select>
						</div>

						<div class="col-12">
							<div class="mb-2">
								<button type="button" class="btn btn-outline-primary" id="autoCalcBtn">
									<i class="bi bi-calculator"></i><?php echo lang('ssp.auto.calc'); ?>
								</button>
							</div>
							<div class="table-responsive">
								<table class="table table-bordered table-sm" id="hourGrid">
									<thead>
										<tr id="hourGridHead"></tr>
									</thead>
									<tbody id="hourGridBody"></tbody>
									<tfoot>
										<tr id="hourGridSum"></tr>
									</tfoot>
								</table>
							</div>
						</div>

						<div class="col-12">
							<hr />
							<h3><?php echo lang('ssp.assignments'); ?></h3>
							<div id="bindingRules"></div>
							<button type="button" class="btn btn-outline-primary" id="addRuleBtn">
								<i class="bi bi-plus"></i><?php echo lang('ssp.add.assignment'); ?>
							</button>
						</div>

					</div>
				</div>
				<div class="card-footer">
					<button class="btn btn-outline-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i><?php echo lang('button.save'); ?></button>
					<a class="btn btn-outline-primary" href="/sspProfile"><i class="bi bi-x-circle"></i><?php echo lang('button.cancel'); ?></a>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	var HOUR_TARGET = <?php echo Ssp_server_model::HOUR_TARGET; ?>;
	var allServers = <?php echo $servers_json; ?>; // [{id, name, priority}]
	var profileRows = <?php echo $profile_rows_json; ?>; // [{ssp_server_id, server_name, server_priority, h:[24]}]
	var bindingGroups = <?php echo $binding_groups_json; ?>; // [{effective_date_start, effective_date_end, weekday, player_ids:[]}]
	var players = <?php echo json_encode(array_map(function ($p) {
						return array('id' => (int)$p->id, 'name' => $p->name);
					}, $players)); ?>;
	var playersOtherBindings = <?php echo $player_bindings_json; ?>; // {player_id: [{profile_name, effective_date_start, effective_date_end, weekday}]}

	var gridValues = {}; // server_id -> array(24)
	var ruleSeq = 0;

	var weekdayLabels = ['<?php echo lang('mon'); ?>', '<?php echo lang('tue'); ?>', '<?php echo lang('wed'); ?>', '<?php echo lang('thu'); ?>', '<?php echo lang('fri'); ?>', '<?php echo lang('sat'); ?>', '<?php echo lang('sun'); ?>'];

	$(document).ready(function() {
		// fill server select options; inactive servers cannot be newly added,
		// but ones already used by this profile stay selectable so they can
		// still be removed
		var usedIds = profileRows.map(function(r) {
			return Number(r.ssp_server_id);
		});
		var $serverSelect = $('#server-select-options');
		$.each(allServers, function(i, s) {
			var label = s.name + ' (' + s.priority + ')';
			var $opt = $('<option>').val(s.id).text(label);
			if (!s.is_active) {
				$opt.text(label + ' - <?php echo lang('ssp.inactive'); ?>');
				if (usedIds.indexOf(Number(s.id)) < 0) {
					$opt.prop('disabled', true);
				}
			}
			$serverSelect.append($opt);
		});

		// restore saved rows
		$.each(profileRows, function(i, row) {
			gridValues[row.ssp_server_id] = row.h;
		});
		if (profileRows.length) {
			$serverSelect.val(profileRows.map(function(r) {
				return String(r.ssp_server_id);
			}));
		}

		$serverSelect.on('change', function() {
			var selected = ($(this).val() || []).map(Number);
			// reset values of deselected servers
			$.each(Object.keys(gridValues), function(i, sid) {
				if (selected.indexOf(Number(sid)) < 0) {
					delete gridValues[sid];
				}
			});
			rebuildGrid();
		});

		buildGridHead();
		rebuildGrid();

		// binding rules
		if (bindingGroups.length) {
			$.each(bindingGroups, function(i, g) {
				addRuleCard(g);
			});
		}

		$('#addRuleBtn').on('click', function() {
			addRuleCard();
		});

		$('#autoCalcBtn').on('click', autoCalc);

		$('#profileForm').on('submit', function(e) {
			e.preventDefault();
			saveProfile();
			return false;
		});
	});

	function serverById(id) {
		for (var i = 0; i < allServers.length; i++) {
			if (allServers[i].id == id) return allServers[i];
		}
		return null;
	}

	function buildGridHead() {
		var $head = $('#hourGridHead');
		$head.empty();
		$head.append($('<th>').text('<?php echo lang('ssp.server'); ?>'));
		for (var h = 0; h < 24; h++) {
			$head.append($('<th>').text(h));
		}
	}

	function selectedServerIds() {
		// NOTE: .val() skips disabled (inactive) options while :selected does
		// not - inactive servers already in this profile must stay counted
		return $('#server-select-options option:selected').map(function() {
			return Number(this.value);
		}).get();
	}

	function rebuildGrid() {
		var ids = selectedServerIds();
		var $body = $('#hourGridBody');
		$body.empty();

		$.each(ids, function(i, sid) {
			if (!gridValues[sid]) {
				gridValues[sid] = new Array(24).fill(0);
			}
			var server = serverById(sid);
			var $tr = $('<tr>').attr('data-sid', sid);
			var serverLabel = server ? server.name : sid;
			if (server && !server.is_active) {
				serverLabel += ' (<?php echo lang('ssp.inactive'); ?>)';
			}
			$tr.append($('<td>').text(serverLabel));
			for (var h = 0; h < 24; h++) {
				var $input = $('<input type="number" min="0" max="' + HOUR_TARGET + '" class="form-control form-control-sm hour-input" />')
					.attr('data-hour', h)
					.val(gridValues[sid][h])
					.on('input change', function() {
						var $this = $(this);
						var rowSid = Number($this.closest('tr').attr('data-sid'));
						var hour = Number($this.attr('data-hour'));
						var v = parseInt($this.val(), 10);
						if (isNaN(v) || v < 0) v = 0;
						if (v > HOUR_TARGET) v = HOUR_TARGET;
						gridValues[rowSid][hour] = v;
						refreshSums();
					});
				$tr.append($('<td>').append($input));
			}
			$body.append($tr);
		});

		refreshSums();
	}

	function hourSums() {
		var sums = new Array(24).fill(0);
		$.each(gridValues, function(sid, hours) {
			for (var h = 0; h < 24; h++) {
				sums[h] += Number(hours[h]) || 0;
			}
		});
		return sums;
	}

	function refreshSums() {
		var sums = hourSums();
		var $sumRow = $('#hourGridSum');
		$sumRow.empty();
		$sumRow.append($('<td>').text('<?php echo lang('ssp.sum'); ?>'));
		for (var h = 0; h < 24; h++) {
			var $cell = $('<span class="sum-cell">').text(sums[h]);
			if (selectedServerIds().length && sums[h] != HOUR_TARGET) {
				$cell.addClass('sum-bad');
			}
			$sumRow.append($('<td>').append($cell));
		}
	}

	/**
	 * Largest remainder method:
	 * per server raw = priority / sum(all selected priorities) * 10
	 * floor all, then hand out the remaining quota to the largest fractions.
	 */
	function autoCalc() {
		var ids = selectedServerIds();
		if (!ids.length) {
			toastr.error('<?php echo lang('ssp.select.servers'); ?>');
			return;
		}
		var weights = ids.map(function(id) {
			var s = serverById(id);
			return s ? s.priority : 0;
		});
		var total = weights.reduce(function(a, b) {
			return a + b;
		}, 0);
		if (total <= 0) {
			toastr.error('<?php echo lang('ssp.weight.zero'); ?>');
			return;
		}

		var raw = weights.map(function(w) {
			return w / total * HOUR_TARGET;
		});
		var floors = raw.map(Math.floor);
		var remaining = HOUR_TARGET - floors.reduce(function(a, b) {
			return a + b;
		}, 0);

		// indexes ordered by fractional part descending
		var order = raw.map(function(v, i) {
			return {
				i: i,
				frac: v - Math.floor(v)
			};
		}).sort(function(a, b) {
			return b.frac - a.frac;
		});
		for (var k = 0; k < remaining; k++) {
			floors[order[k].i] += 1;
		}

		$.each(ids, function(idx, sid) {
			gridValues[sid] = new Array(24).fill(floors[idx]);
		});
		rebuildGrid();
		toastr.success('<?php echo lang('ssp.auto.calc.done'); ?>');
	}

	/* ---------------- binding rules ---------------- */

	function addRuleCard(group) {
		group = group || {};
		var rid = 'rule_' + (++ruleSeq);
		var playersSelId = rid + '_players';
		var $card = $('<div class="rule-card">').attr('id', rid);

		// card header: title + remove button
		$card.append(
			$('<div class="rule-head">').append(
				$('<span class="rule-title">').text('<?php echo lang('ssp.assignment'); ?> #' + ruleSeq),
				$('<button type="button" class="btn btn-sm btn-outline-danger rule-remove"><i class="bi bi-trash"></i><?php echo lang('delete'); ?></button>')
			)
		);

		// row 1: players select (full width) with a search button at the end of the row
		var $players = $('<select class="form-select select2 rule-players" multiple></select>').attr('id', playersSelId);
		$.each(players, function(i, p) {
			if (!p.name) return;
			var $opt = $('<option>').val(p.id).text(p.name);
			if (group.player_ids && group.player_ids.map(Number).indexOf(p.id) >= 0) {
				$opt.attr('selected', 'selected');
			}
			$players.append($opt);
		});
		var $playersRow = $('<div class="row g-2 mb-2 align-items-start">');
		$playersRow.append($('<div class="col-12">').append(
			$('<label>').text('<?php echo lang('player'); ?>')
		));
		$playersRow.append($('<div class="col">').append($players));
		$playersRow.append($('<div class="col-auto">').append(
			$('<button type="button" class="btn btn-outline-primary">').attr({
				'data-bs-toggle': 'modal',
				'data-bs-target': '#playerModal',
				'data-target-field': playersSelId
			}).html('<i class="bi bi-search"></i><?php echo lang('ssp.select.players'); ?>')
		));

		// row 2: date range switch + dates + weekday checkboxes in one row
		// switch stays unchecked (no date range) unless a real range was saved
		var hasRange = hasDateRange(group.effective_date_start, group.effective_date_end);
		var $row2 = $('<div class="row g-2 mb-2 align-items-end">');
		$row2.append($('<div class="col-auto">').append(
			$('<label class="form-check form-switch">').append(
				$('<input type="checkbox" class="form-check-input rule-daterange">').prop('checked', hasRange),
				$('<span class="form-check-label">').text('<?php echo lang('ssp.date.range'); ?>')
			)
		));
		$row2.append($('<div class="col-md-2">').append(
			$('<label>').text('<?php echo lang('start.date'); ?>'),
			$('<input type="date" class="form-control rule-start">').val(hasRange ? group.effective_date_start : '').prop('disabled', !hasRange)
		));
		$row2.append($('<div class="col-md-2">').append(
			$('<label>').text('<?php echo lang('end.date'); ?>'),
			$('<input type="date" class="form-control rule-end">').val(hasRange ? group.effective_date_end : '').prop('disabled', !hasRange)
		));

		// weekday checkboxes in the same row
		var $weekCol = $('<div class="col">');
		$weekCol.append($('<label class="d-block">').text('<?php echo lang('ssp.weekdays'); ?>'));
		var weekday = (typeof group.weekday != 'undefined') ? Number(group.weekday) : 127;
		for (var d = 0; d < 7; d++) {
			var $cb = $('<input type="checkbox" class="form-check-input rule-weekday me-1">').attr('data-day', d);
			if (weekday & (1 << d)) {
				$cb.prop('checked', true);
			}
			$weekCol.append(
				$('<label class="form-check form-check-inline">').append($cb, $('<span class="form-check-label">').text(weekdayLabels[d]))
			);
		}
		$row2.append($weekCol);

		$card.append($playersRow, $row2);
		$('#bindingRules').append($card);

		$players.select2({
			theme: 'bootstrap-5',
			width: '100%'
		});

		$card.find('.rule-daterange').on('change', function() {
			var enabled = $(this).is(':checked');
			$card.find('.rule-start, .rule-end').prop('disabled', !enabled);
			if (!enabled) {
				$card.find('.rule-start, .rule-end').val('');
			}
		});

		$card.find('.rule-remove').on('click', function() {
			$card.remove();
			renumberRuleCards();
		});
	}

	// a binding counts as "with date range" only when a real range was saved;
	// the placeholders below all mean "no limit" -> switch stays unchecked
	function hasDateRange(start, end) {
		var placeholders = ['', '1970-01-01', '2099-12-31', '9999-12-31', '0000-00-00'];
		if (!start || placeholders.indexOf(start) >= 0) {
			return false;
		}
		if (!end || placeholders.indexOf(end) >= 0) {
			return false;
		}
		return true;
	}

	// keep rule card titles consecutive (#1, #2, ...) after removing a card
	function renumberRuleCards() {
		var ruleTitle = '<?php echo lang('ssp.assignment'); ?> ';
		$('#bindingRules .rule-card').each(function(i) {
			$(this).find('.rule-title').text(ruleTitle + '#' + (i + 1));
		});
	}

	function collectRule(card) {
		var $card = $(card);
		var playerIds = ($card.find('.rule-players').val() || []).map(Number).filter(function(v) {
			return v > 0;
		});
		if (!playerIds.length) {
			return null;
		}

		var weekday = 0;
		$card.find('.rule-weekday:checked').each(function() {
			weekday |= (1 << Number($(this).attr('data-day')));
		});
		if (!weekday) {
			toastr.error('<?php echo lang('ssp.weekday.required'); ?>');
			return false;
		}

		var hasRange = $card.find('.rule-daterange').is(':checked');
		var start = hasRange ? $card.find('.rule-start').val() : '';
		var end = hasRange ? $card.find('.rule-end').val() : '';
		if (hasRange && (!start || !end)) {
			toastr.error('<?php echo lang('ssp.date.required'); ?>');
			return false;
		}
		if (hasRange && start > end) {
			toastr.error('<?php echo lang('ssp.date.order'); ?>');
			return false;
		}

		return {
			player_ids: playerIds,
			effective_date_start: start,
			effective_date_end: end,
			weekday: weekday
		};
	}

	function saveProfile() {
		var name = $.trim($('#profile_name').val());
		var oldName = $('#old_name').val();
		if (!name) {
			toastr.error('<?php echo sprintf(lang('field.required'), lang('name')); ?>');
			return;
		}

		var ids = selectedServerIds();
		if (!ids.length) {
			toastr.error('<?php echo lang('ssp.select.servers'); ?>');
			return;
		}

		var sums = hourSums();
		var badHours = [];
		for (var h = 0; h < 24; h++) {
			if (sums[h] != HOUR_TARGET) {
				badHours.push('h' + h + '(' + sums[h] + ')');
			}
		}
		if (badHours.length) {
			toastr.error('<?php echo lang('ssp.hour.sum.check'); ?>: ' + badHours.join(', '));
			return;
		}

		var rules = [];
		var invalid = false;
		$('#bindingRules .rule-card').each(function() {
			var rule = collectRule(this);
			if (rule === false) {
				invalid = true;
				return false;
			}
			if (rule) {
				rules.push(rule);
			}
		});
		if (invalid) {
			return;
		}

		var conflicts = checkAssignmentConflicts(rules);
		if (conflicts.range.length) {
			toastr.error('<?php echo lang('ssp.assignment.conflict'); ?>'.replace('%s', conflicts.range.join(', ')));
			return;
		}
		if (conflicts.nolimit.length) {
			toastr.error('<?php echo lang('ssp.assignment.nolimit'); ?>'.replace('%s', conflicts.nolimit.join(', ')));
			return;
		}

		var servers = ids.map(function(sid) {
			return {
				ssp_server_id: sid,
				h: gridValues[sid]
			};
		});

		var newName = name;
		var doSave = function() {
			$.post('/sspProfile/do_save', {
				name: newName,
				old_name: oldName,
				servers_json: JSON.stringify(servers),
				bindings_json: JSON.stringify(rules)
			}, function(data) {
				if (data.code != 0) {
					toastr.error(data.msg);
				} else {
					localStorage.setItem("Status", JSON.stringify({
						type: 'success',
						message: data.msg
					}));
					window.location.href = '/sspProfile';
				}
			}, 'json');
		};

		if (newName != oldName) {
			// pre-check duplicated name on rename / create
			$.post('/sspProfile/name_exists', {
				name: newName,
				exclude: oldName
			}, function(data) {
				if (data.code != 0) {
					toastr.error('<?php echo lang('ssp.name.exists'); ?>'.replace('%s', newName));
				} else {
					doSave();
				}
			}, 'json');
		} else {
			doSave();
		}
	}

	/* a date range is "real" when it is set and not a no-limit placeholder */
	function isRealRange(start, end) {
		var placeholders = ['', '1970-01-01', '2099-12-31', '9999-12-31', '0000-00-00'];
		return !!start && !!end && placeholders.indexOf(start) < 0 && placeholders.indexOf(end) < 0;
	}

	/* player id -> display name, falls back to the id */
	function playerName(pid) {
		for (var i = 0; i < players.length; i++) {
			if (players[i].id == pid) {
				return players[i].name;
			}
		}
		return '#' + pid;
	}

	/**
	 * Two assignments of the same player conflict when both have a real date
	 * range, the ranges overlap AND the weekday masks intersect. Additionally
	 * a player may have at most ONE assignment without a date range.
	 * Same weekday with disjoint dates (or vice versa) is allowed.
	 * Checks the cards against each other and against existing bindings of
	 * OTHER profiles. Returns {range: [...], nolimit: [...]} of player labels.
	 */
	function checkAssignmentConflicts(rules) {
		var oldName = $('#old_name').val();
		var rangeConflicts = [];
		var nolimitConflicts = [];
		$.each(rules, function(i, rule) {
			var real = isRealRange(rule.effective_date_start, rule.effective_date_end);
			$.each(rule.player_ids, function(j, pid) {
				// 1) against the other cards of this profile
				for (var k = i + 1; k < rules.length; k++) {
					var other = rules[k];
					if (other.player_ids.indexOf(pid) < 0) continue;
					var otherReal = isRealRange(other.effective_date_start, other.effective_date_end);
					if (!real && !otherReal) {
						// a player may have at most one assignment without date range
						nolimitConflicts.push(playerName(pid));
						break;
					}
					if (!real || !otherReal) continue;
					if (rule.effective_date_start <= other.effective_date_end
						&& other.effective_date_start <= rule.effective_date_end
						&& (rule.weekday & other.weekday)) {
						rangeConflicts.push(playerName(pid));
						break;
					}
				}
				// 2) against existing bindings in other profiles
				var others = playersOtherBindings[pid] || [];
				for (var m = 0; m < others.length; m++) {
					var b = others[m];
					if (b.profile_name == oldName) continue; // this profile's bindings are replaced on save
					var bReal = isRealRange(b.effective_date_start, b.effective_date_end);
					if (!real) {
						if (!bReal) {
							nolimitConflicts.push(playerName(pid) + ' (' + b.profile_name + ')');
							break;
						}
						continue;
					}
					if (!bReal) continue;
					if (rule.effective_date_start <= b.effective_date_end
						&& b.effective_date_start <= rule.effective_date_end
						&& (rule.weekday & Number(b.weekday))) {
						rangeConflicts.push(playerName(pid) + ' (' + b.profile_name + ')');
						break;
					}
				}
			});
		});
		return {
			range: dedupeList(rangeConflicts),
			nolimit: dedupeList(nolimitConflicts)
		};
	}

	/* drop duplicates (same player hit by several conflicts) */
	function dedupeList(list) {
		return list.filter(function(v, i) {
			return list.indexOf(v) === i;
		});
	}
</script>