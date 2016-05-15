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

import React from 'react';
import { shallow } from 'enzyme';
import { expect } from 'chai';
import FormField from '../../../components/form/FormField';
import FormHelper from '../../../util/react/FormHelper';
import FormControl from 'react-bootstrap/lib/FormControl';

describe('FormField', () => {
  it('renders a simple text field into the composite', () => {
    const markup = shallow((
      <FormField type="text" value="test" errors={{}} name="test" helper={new FormHelper({}, {}, {}, () => {}, 'namespace')} />
    ));

    expect(markup.contains(<FormControl.Feedback />)).to.equal(true);
    expect(markup.find('FormControl').prop('name')).to.equal('test');
    expect(markup.find('FormControl').prop('type')).to.equal('text');
    expect(markup.find('FormControl').prop('main')).to.equal(true);
    expect(markup.find('FormControlFeedback').prop('main')).to.not.equal(true);
  });
});
