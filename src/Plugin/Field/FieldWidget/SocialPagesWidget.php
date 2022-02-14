<?php

namespace Drupal\social_pages\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Drupal\Component\Utility\NestedArray;

/**
 * Plugin implementation of the 'social_pages_widget' widget.
 *
 * @FieldWidget(
 *   id = "social_pages_widget",
 *   module = "social_pages",
 *   label = @Translation("Social Pages"),
 *   field_types = {
 *     "social_pages"
 *   }
 * )
 */
class SocialPagesWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {

    $defaults['enabled_networks'] = array_keys(_social_pages_field_defintions());
    return $defaults + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    foreach(_social_pages_field_defintions() as $key => $item){
      $options[$key] = $item['title'];
    }

    $elements['enabled_networks'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Enabled Networks'),
      '#options' => $options,
      '#default_value' => $this->getSetting('enabled_networks'),
      '#description' => $this->t('Choose all the networks to enable for this field.'),
    ];


    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $enabled = array_filter($this->getSetting('enabled_networks'));
    $summary_networks = [];
    foreach(_social_pages_field_defintions() as $key => $item){
      if(in_array($key, $enabled)){
        $summary_networks = $item['title'];
      }
    }
    if (!empty($enabled)) {
      $summary[] = $this->t('Enabled Networks: @networks', [
        '@networks' => implode(', ', $summary_networks),
      ]);
    } else {
      $summary[] = $this->t('No Enabled Networks.');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $enabled = $this->getSetting('enabled_networks');

    $element['#type'] = 'fieldset';
    $element['#attributes']['class'][] = 'form-wrapper';

    foreach(_social_pages_field_defintions() as $key => $item){
      if(!empty($enabled[$key])){
        $element[$key] = [
          '#type' => 'textfield',
          '#title' => [
            '#theme' => 'svg_icon',
            '#style' => 'brands',
            '#name' => $item['icon'],
            '#suffix' => $this->t($item['title']),
          ],
          '#default_value' => isset($items[$delta]->{$key}) ? $items[$delta]->{$key} : NULL,
          '#placeholder' => $item['placeholder'],
        ];
      }
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function errorElement(array $element, ConstraintViolationInterface $error, array $form, FormStateInterface $form_state) {
    // Return the sub element field the failed validation.
    if (!empty($error->arrayPropertyPath) && $sub_element = NestedArray::getValue($element, $error->arrayPropertyPath)) {
      return $sub_element;
    } else {
      return $element;
    }
  }

}
