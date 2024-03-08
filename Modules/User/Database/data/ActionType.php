<?php
namespace Modules\User\Database\data;

enum ActionType: string {
    case Attach = 'attach';
    case Detach = 'detach';
}
