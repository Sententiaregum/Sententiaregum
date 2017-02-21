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

import { expect } from 'chai';
import DropDownItem from '../../../../components/app/markup/DropDownItem';
import React from 'react';
import { shallow } from 'enzyme';

describe('DropDownItem', () => {
  it('renders a dropdown item including the given properties', () => {
    const markup = shallow((
      <DropDownItem
        isActive={true}
        id="test"
        displayName="Test"
      />
    ));

    expect(markup.hasClass('active')).to.equal(true);
    expect(markup.prop('id')).to.equal('test');
    expect(markup.contains('Test')).to.equal(true);
  });
});
