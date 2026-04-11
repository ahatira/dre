<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_diagnostic\Unit\Plugin\Validation\Constraint;

use Drupal\Core\TypedData\TypedDataInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_diagnostic\Entity\PsDiagnosticInterface;
use Drupal\ps_diagnostic\Plugin\Field\FieldType\DiagnosticItem;
use Drupal\ps_diagnostic\Plugin\Validation\Constraint\DiagnosticValidConstraint;
use Drupal\ps_diagnostic\Plugin\Validation\Constraint\DiagnosticValidConstraintValidator;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Unit tests for DiagnosticValidConstraintValidator.
 *
 * @coversDefaultClass \Drupal\ps_diagnostic\Plugin\Validation\Constraint\DiagnosticValidConstraintValidator
 * @group ps_diagnostic
 */
class DiagnosticValidConstraintValidatorTest extends UnitTestCase {

  /**
   * The mocked entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The validator under test.
   *
   * @var \Drupal\ps_diagnostic\Plugin\Validation\Constraint\DiagnosticValidConstraintValidator
   */
  protected DiagnosticValidConstraintValidator $validator;

  /**
   * The mocked execution context.
   *
   * @var \Symfony\Component\Validator\Context\ExecutionContextInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected ExecutionContextInterface $context;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $this->validator = new DiagnosticValidConstraintValidator($this->entityTypeManager);
    $this->context = $this->createMock(ExecutionContextInterface::class);
    $this->validator->initialize($this->context);
  }

  /**
   * Tests validation passes when no type is selected (empty field).
   *
   * @covers ::validate
   */
  public function testValidateEmptyField(): void {
    $value = $this->createMockDiagnosticItem('', NULL, '', FALSE);
    $constraint = new DiagnosticValidConstraint();

    $this->context->expects($this->never())
      ->method('buildViolation');

    $this->validator->validate($value, $constraint);
  }

  /**
   * Tests validation fails when type is selected but no value/class and no_classification=false.
   *
   * @covers ::validate
   */
  public function testValidateTypeSelectedButNoDataAndNoFlag(): void {
    $value = $this->createMockDiagnosticItem('dpe', NULL, '', FALSE);
    $constraint = new DiagnosticValidConstraint();

    $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
    $violationBuilder->expects($this->once())
      ->method('atPath')
      ->with('value')
      ->willReturnSelf();
    $violationBuilder->expects($this->once())
      ->method('addViolation');

    $this->context->expects($this->once())
      ->method('buildViolation')
      ->with($constraint->missingData)
      ->willReturn($violationBuilder);

    $this->validator->validate($value, $constraint);
  }

  /**
   * Tests validation passes when type is selected and no_classification=true.
   *
   * @covers ::validate
   */
  public function testValidateTypeSelectedWithNoClassificationFlag(): void {
    $value = $this->createMockDiagnosticItem('dpe', NULL, '', TRUE);
    $constraint = new DiagnosticValidConstraint();

    $this->context->expects($this->never())
      ->method('buildViolation');

    $this->validator->validate($value, $constraint);
  }

  /**
   * Tests validation passes when type and value are provided.
   *
   * @covers ::validate
   */
  public function testValidateTypeAndValueProvided(): void {
    $value = $this->createMockDiagnosticItem('dpe', 150.0, '', FALSE);
    $constraint = new DiagnosticValidConstraint();

    $this->context->expects($this->never())
      ->method('buildViolation');

    $this->validator->validate($value, $constraint);
  }

  /**
   * Tests validation passes when type and class are provided.
   *
   * @covers ::validate
   */
  public function testValidateTypeAndClassProvided(): void {
    $value = $this->createMockDiagnosticItem('dpe', NULL, 'D', FALSE);
    $constraint = new DiagnosticValidConstraint();

    $this->context->expects($this->never())
      ->method('buildViolation');

    $this->validator->validate($value, $constraint);
  }

  /**
   * Tests validation passes when value and class are coherent.
   *
   * @covers ::validate
   */
  public function testValidateCoherentValueAndClass(): void {
    $value = $this->createMockDiagnosticItem('dpe', 150.0, 'D', FALSE);
    $constraint = new DiagnosticValidConstraint();

    // Mock diagnostic that calculates class D for value 150.
    $diagnostic = $this->createMock(PsDiagnosticInterface::class);
    $diagnostic->expects($this->once())
      ->method('calculateClass')
      ->with(150.0)
      ->willReturn('D');

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->expects($this->once())
      ->method('load')
      ->with('dpe')
      ->willReturn($diagnostic);

    $this->entityTypeManager->expects($this->once())
      ->method('getStorage')
      ->with('diagnostic')
      ->willReturn($storage);

    $this->context->expects($this->never())
      ->method('buildViolation');

    $this->validator->validate($value, $constraint);
  }

  /**
   * Tests validation fails when value and class are incoherent.
   *
   * @covers ::validate
   */
  public function testValidateIncoherentValueAndClass(): void {
    $value = $this->createMockDiagnosticItem('dpe', 150.0, 'A', FALSE);
    $constraint = new DiagnosticValidConstraint();

    // Mock diagnostic that calculates class D for value 150.
    $diagnostic = $this->createMock(PsDiagnosticInterface::class);
    $diagnostic->expects($this->once())
      ->method('calculateClass')
      ->with(150.0)
      ->willReturn('D');

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->expects($this->once())
      ->method('load')
      ->with('dpe')
      ->willReturn($diagnostic);

    $this->entityTypeManager->expects($this->once())
      ->method('getStorage')
      ->with('diagnostic')
      ->willReturn($storage);

    $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
    $violationBuilder->expects($this->once())
      ->method('setParameters')
      ->with([
        '%provided' => 'A',
        '%calculated' => 'D',
        '%value' => 150.0,
      ])
      ->willReturnSelf();
    $violationBuilder->expects($this->once())
      ->method('atPath')
      ->with('class')
      ->willReturnSelf();
    $violationBuilder->expects($this->once())
      ->method('addViolation');

    $this->context->expects($this->once())
      ->method('buildViolation')
      ->with($constraint->incoherentClass)
      ->willReturn($violationBuilder);

    $this->validator->validate($value, $constraint);
  }

  /**
   * Creates a mock DiagnosticItem with specified values.
   *
   * @param string $typeId
   *   The type ID.
   * @param float|null $valueNumeric
   *   The numeric value.
   * @param string $labelCode
   *   The label code.
   * @param bool $noClassification
   *   The no classification flag.
   *
   * @return \Drupal\ps_diagnostic\Plugin\Field\FieldType\DiagnosticItem|\PHPUnit\Framework\MockObject\MockObject
   *   The mocked diagnostic item.
   */
  protected function createMockDiagnosticItem(
    string $typeId,
    ?float $valueNumeric,
    string $labelCode,
    bool $noClassification,
  ): DiagnosticItem {
    $item = $this->createMock(DiagnosticItem::class);

    $typeProperty = $this->createMock(TypedDataInterface::class);
    $typeProperty->method('getValue')->willReturn($typeId);

    $valueProperty = $this->createMock(TypedDataInterface::class);
    $valueProperty->method('getValue')->willReturn($valueNumeric);

    $labelProperty = $this->createMock(TypedDataInterface::class);
    $labelProperty->method('getValue')->willReturn($labelCode);

    $noClassProperty = $this->createMock(TypedDataInterface::class);
    $noClassProperty->method('getValue')->willReturn($noClassification);

    $item->method('get')
      ->willReturnMap([
        ['type_id', $typeProperty],
        ['value', $valueProperty],
        ['class', $labelProperty],
        ['no_classification', $noClassProperty],
      ]);

    return $item;
  }

}
