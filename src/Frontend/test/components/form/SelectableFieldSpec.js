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
import SelectableField from '../../../components/form/SelectableField';
import FormHelper from '../../../util/react/FormHelper';

describe('SelectableField', () => {
  it('renders a selectable into the wrapper', () => {
    const markup = shallow((
      <SelectableField options={{ foo: 'bar', bar: 'baz' }} value="test" errors={{}} name="test" helper={new FormHelper({}, {}, {}, () => {}, 'namespace')} />
    ));

    expect(markup.find('FormControl').contains([
      <option value="foo">bar</option>,
      <option value="bar">baz</option>
    ])).to.equal(true);

    expect(markup.find('FormControl').prop('componentClass')).to.equal('select');
    expect(markup.find('FormControl').prop('name')).to.equal('test');
  });
});
