/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
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
    const markup = shallow(<MenuItem url="/#/foo" label="FooBar" key={1} isActive={true} />);
    expect(markup.prop('href')).to.equal('/#/foo');
    expect(markup.find('Translate').prop('content')).to.equal('FooBar');
    expect(markup.prop('active')).to.equal(true);
  });
});
