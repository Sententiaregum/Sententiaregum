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
import MenuItem from '../../../../components/app/markup/MenuItem';
import { expect } from 'chai';

describe('MenuItem', () => {
  it('builds a menu item from configuration', () => {
    const markup = shallow(<MenuItem url="/#/foo" label="FooBar" key={1} />);
    expect(markup.prop('href')).to.equal('/#/foo');
    expect(markup.find('Translate').prop('content')).to.equal('FooBar');
  });
});
