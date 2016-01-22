/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import FormComponent from '../../../components/app/FormComponent';
import sinon from 'sinon';
import chai from 'chai';
import translator from 'counterpart';

describe('FormComponent', () => {
  it('builds translations', () => {
    const instance = new FormComponent();
    sinon.stub(instance, '_getFormFields', () => ['field']);

    sinon.stub(translator, 'translate', field => field);
    const result = instance._buildTranslationComponents();
    chai.expect(typeof result.field).to.equal('string');

    translator.translate.restore();
    instance._getFormFields.restore();
  });

  it('renders bootstrap styles', () => {
    const instance = new FormComponent();
    sinon.stub(instance, '_getFormFields', () => ['field', 'invalid_field']);

    instance.state = {
      validation: {
        errors: {
          invalid_field: ['Validation...']
        },
        submitted: true
      }
    };

    const result = instance._getBootstrapStyles(['field1'], ['field']);
    chai.expect(result['field1Style']).to.equal('success');
    chai.expect(result['invalid_fieldStyle']).to.equal('error');
    chai.expect(result['field']).to.equal(undefined);

    instance._getFormFields.restore();
  });

  it('renders the validation errors', () => {
    const instance = new FormComponent();
    sinon.stub(instance, '_getFormFields', () => ['invalid_field']);

    instance.state = {
      validation: {
        errors: {
          invalid_field: ['Validation...']
        },
        submitted: true
      }
    };

    const result = instance._renderErrors();
    const item   = result['invalid_field'];
    const span   = item.props.children[0].props.children;

    chai.expect(span.props.className).to.equal('help-text');
    chai.expect(span.props.children).to.equal('Validation...');

    instance._getFormFields.restore();
  });
});
