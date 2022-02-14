<?php

namespace Drupal\social_pages\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'social_pages_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "social_pages_formatter",
 *   label = @Translation("Social pages formatter"),
 *   field_types = {
 *     "social_pages"
 *   }
 * )
 */
class SocialPagesFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'display' => ['title'],
      'link' => TRUE,
      'enabled_networks' => social_pages_field_options(),
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $elements['enabled_networks'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Enabled Networks'),
      '#options' => social_pages_field_options(),
      '#default_value' => $this->getSetting('enabled_networks'),
      '#description' => t('Choose all the networds to enable for this field.'),
    ];

    $elements['display'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Show'),
      '#options' => [
        'icons' => 'Icons',
        'title' => 'The network name',
        'url' => 'The url to the account',
        'handle' => 'The username @handle (not available for all networks)',
      ],
      '#default_value' => $this->getSetting('display'),
    ];

    $elements['link'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Link to the social account'),
      '#default_value' => $this->getSetting('link'),
    ];

    return $elements + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $enabled = array_filter($this->getSetting('enabled_networks'));
    $display = array_filter($this->getSetting('display'));
    if (!empty($enabled)) {
      $summary[] = $this->t('Enabled Networks: @networks', [
        '@networks' => implode(', ', $enabled),
      ]);
      $summary[] = $this->t('Display: @elements', [
        '@elements' => implode(', ', $display),
      ]);
      $summary[] = $this->t('Lined: @linked', [
        '@linked' => $this->getSetting('link') ? $this->t('Yes') : $this->t('No'),
      ]);
    } else {
      $summary[] = $this->t('No enabled networks. Nothing will be displayed.');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
      $elements[$delta] = $this->viewValue($item);
    }
    $elements['#attached']['library'][] = 'social_pages/icon-styles';
    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   * Wich contains a list of enabled networks.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return array
   *   A render array for all the enabled networks.
   */
  protected function viewValue(FieldItemInterface $item) {

    $enabled = array_filter($this->getSetting('enabled_networks'));
    if(!$enabled){
      return [];
    }
    $info = _social_pages_field_defintions();
    $output = [];
    foreach ($enabled as $network) {
      if (!empty($item->{$network})) {
        $output[$network] = [
          '#theme' => 'social_pages_network',
          '#display' => array_filter($this->getSetting('display')),
          '#link' => (bool) $this->getSetting('link'),
          '#id' => $network,
          '#value' => $item->{$network},
          '#info' => $info[$network],
          '#url' => str_replace('!value', $item->{$network}, $info[$network]['render_pattern']),
          '#title' => $info[$network]['title'],
        ];
      }
    }
    return $output;
  }

}
