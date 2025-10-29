<div class="modal modal-blur fade" id="modal-report" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Command</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <div>
                        <label class="form-label"><?php echo lang('name'); ?></label>
                        <input type="text" class="form-control" name="name" placeholder="">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label"><?php echo lang('command'); ?></label>
                    <input type="text" id="command" class="form-control" name="command" data-mask="00 00 00 00 00 00" placeholder="">
                </div>
                <div class="mb-3">
                    <div class="form-label">Automation</div>
                    <div>
                        <label class="form-check">
                            <input class="form-check-input" type="radio" name="radios" checked>
                            <span class="form-check-label">Mamually</span>
                        </label>
                        <label class="form-check">
                            <input class="form-check-input" type="radio" name="radios">
                            <span class="form-check-label">Daily At</span>
                            <input type="time" class="form-control" name="time" placeholder="">
                        </label>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                    Cancel
                </a>
                <a href="#" class="btn btn-primary ms-auto" data-bs-dismiss="modal" onclick="">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Create new command
                </a>
            </div>
        </div>
    </div>
</div>