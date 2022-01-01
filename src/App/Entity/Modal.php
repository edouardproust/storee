<?php

namespace App\App\Entity;

class Modal
{
    private $modalId;

    public function __construct(string $modalId)
    {
        $this->modalId = $modalId;
    }

    public function showPopup(?string $title = null, ?string $bodyHtml = null, string $actionBtnLabel = null, ?string $actionBtnUrl = '#'): string
    {
        $title = $title ? '<h5 class="modal-title" id="staticBackdropLabel">'.$title.'</h5>' : '';
        $bodyHtml = $bodyHtml ?? "Are you sure? This action can not be reversed.";
        $actionBtnHtml = $actionBtnLabel ? '<a href="'.$actionBtnUrl.'" type="button" class="btn btn-primary">'.$actionBtnLabel.'</a>' : '';

        return '
            <div class="modal fade" id="'.$this->modalId.'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            '.$title.'
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                        '.$bodyHtml.'
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            '.$actionBtnHtml.'
                        </div>
                    </div>
                </div>
            </div>
        ';
    }

    public function showTrigger(string $btnLabel, ?string $btnClass = null): string
    {
        $btnClass = $btnClass ?? 'btn btn-primary';

        return '
            <a href="#" class="'. $btnClass .'" data-bs-toggle="modal" data-bs-target="#'.$this->modalId.'">
                '.$btnLabel.'
            </a>
        ';
    }

}