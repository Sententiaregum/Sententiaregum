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

import { shallow } from 'enzyme';
import React from 'react';
import CompositeFormField from '../../../components/form/CompositeFormField';
import FormHelper from '../../../util/react/FormHelper';
import { expect } from 'chai';
import { stub } from 'sinon';
import Locale from '../../../util/http/Locale';
import HelpBlock from 'react-bootstrap/lib/HelpBlock';

describe('CompositeFormField', () => {
  it('renders a wrapper for a form field including some children', () => {
    const markup = shallow((
      <CompositeFormField
        name="test"
        errors={{}}
        helper={new FormHelper({}, {}, {}, () => {}, 'namespace')}
      >
        <h1>Hello World!</h1>
      </CompositeFormField>
    ));

    expect(markup.prop('validationState')).to.equal(null);
    expect(markup.prop('controlId')).to.equal('test');
    expect(markup.find('h1').contains('Hello World!')).to.equal(true);
  });

  it('renders errors into the markup', () => {
    stub(Locale, 'getLocale', () => 'en');
    const errors = { test: { en: ['Error #1', 'Error #2'] } };
    const helper = new FormHelper({}, {}, {}, () => {}, 'namespace');
    helper.getInitialState(errors);

    const markup = shallow((
      <CompositeFormField
        name="test"
        errors={errors}
        helper={helper}
      >
        <h1>Hello World!</h1>
      </CompositeFormField>
    ));

    expect(markup.contains([
      <HelpBlock>Error #1</HelpBlock>,
      <HelpBlock>Error #2</HelpBlock>
    ])).to.equal(true);

    Locale.getLocale.restore();
  });
});
