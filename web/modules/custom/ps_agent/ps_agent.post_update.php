<?php

/**
 * @file
 */

declare(strict_types=1);

/**
 * Apply Agent reference and display wiring on existing installations.
 */
function ps_agent_post_update_offer_agent_reference_and_view_mode(): string
{
    require_once __DIR__ . '/ps_agent.install';
    ps_agent_ensure_offer_agent_reference();
    ps_agent_ensure_agent_default_displays();
    ps_agent_ensure_agent_card_view_mode();

    return 'Ensured Offer->Agent reference field and Agent card view mode/display.';
}

/**
 * Ensure default Agent displays are available in Field UI.
 */
function ps_agent_post_update_agent_default_displays(): string
{
    require_once __DIR__ . '/ps_agent.install';
    ps_agent_ensure_agent_default_displays();

    return 'Ensured Agent default form and view displays.';
}

/**
 * Ensure configurable Agent fields exist on existing sites.
 */
function ps_agent_post_update_agent_configurable_fields(): string
{
    require_once __DIR__ . '/ps_agent.install';
    ps_agent_ensure_agent_configurable_fields();
    ps_agent_ensure_agent_default_displays();

    return 'Ensured Agent configurable fields and display placement.';
}

/**
 * Ensure Agent card view mode uses Layout Builder with agent_card component.
 */
function ps_agent_post_update_agent_card_layout_builder(): string
{
    require_once __DIR__ . '/ps_agent.install';
    ps_agent_ensure_agent_card_view_mode();

    return 'Ensured Agent card view mode uses Layout Builder and agent_card component.';
}

/**
 * Ensure Agent card Layout Builder section uses ps_agent block renderer.
 */
function ps_agent_post_update_agent_card_layout_builder_block_plugin(): string
{
    require_once __DIR__ . '/ps_agent.install';
    ps_agent_ensure_agent_card_view_mode();

    return 'Ensured Agent card Layout Builder uses ps_agent block plugin.';
}
