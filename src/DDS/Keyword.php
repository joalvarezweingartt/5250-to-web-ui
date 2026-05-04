<?php
/**
 * DDS Keyword definitions.
 */
namespace App\DDS;

class Keyword {
    public const DISPLAY = [
        'DSPATR' => 'Display attributes (HI, BL, ND, PC, RI, UL)',
        'COLOR' => 'Color (BLU, WHT, RED, TRQ, GRN, PNK)',
        'HIGHLIGHT' => 'Highlight display',
        'BLINK' => 'Blinking display',
        'RI' => 'Reverse image',
        'UL' => 'Underline',
        'ND' => 'Non-display',
    ];

    public const VALIDATION = [
        'CHECK' => 'Input validation (01=mandatory, 02=digits, 04=alpha)',
        'COMP' => 'Compare with another field',
        'RANGE' => 'Value range constraint',
        'VALUES' => 'List of valid values',
        'ERRMSG' => 'Error message identifier',
        'MSGID' => 'Message identifier for validation',
    ];

    public const EDITING = [
        'EDTCDE' => 'Edit code for output formatting',
        'EDTWRD' => 'Edit word pattern',
        'EDTMSK' => 'Edit mask pattern',
    ];

    public const SUBFILE = [
        'SFL' => 'Subfile record',
        'SFLCTL' => 'Subfile control record',
        'SFLPAG' => 'Records per page',
        'SFLSIZ' => 'Subfile size (total records)',
        'SFLRCDNBR' => 'Relative record number',
        'SFLDSP' => 'Display subfile',
        'SFLDSPCTL' => 'Display subfile control',
        'SFLCLR' => 'Clear subfile',
        'SFLINZ' => 'Initialize subfile',
        'SFLMSG' => 'Message subfile',
        'SFLRNA' => 'Roll up indicator',
        'SFLRRN' => 'Set cursor to RRN',
    ];

    public const WINDOW = [
        'WINDOW' => 'Window definition',
        'WDWBORDER' => 'Window border type',
        'WDWTITLE' => 'Window title',
    ];

    public const COMMAND_KEYS = [
        'CF03' => 'F3=Exit',
        'CF04' => 'F4=Prompt',
        'CF05' => 'F5=Refresh',
        'CF06' => 'F6=Create',
        'CF07' => 'F7=Back',
        'CF08' => 'F8=Forward',
        'CF09' => 'F9=Change',
        'CF10' => 'F10=Search',
        'CF11' => 'F11=Display',
        'CF12' => 'F12=Cancel',
        'CF13' => 'F13=Repeat',
        'CF14' => 'F14=Additional',
        'CF15' => 'F15=Split',
        'CF16' => 'F16=Main menu',
        'CF17' => 'F17=Top',
        'CF18' => 'F18=Bottom',
        'CF19' => 'F19=Left',
        'CF20' => 'F20=Right',
        'CF21' => 'F21=Print',
        'CF22' => 'F22=Command',
        'CF23' => 'F23=User1',
        'CF24' => 'F24=User2',
    ];

    /**
     * Get human-readable label for a command key.
     */
    public static function getCommandKeyLabel(string $key): string {
        return self::COMMAND_KEYS[$key] ?? $key;
    }

    /**
     * Get all keyword groups.
     */
    public static function getAll(): array {
        return [
            'display' => self::DISPLAY,
            'validation' => self::VALIDATION,
            'editing' => self::EDITING,
            'subfile' => self::SUBFILE,
            'window' => self::WINDOW,
            'command_keys' => self::COMMAND_KEYS,
        ];
    }
}
