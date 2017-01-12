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
import Suggestions from '../../../../components/portal/signup/Suggestions';
import { expect } from 'chai';
import { shallow } from 'enzyme';

describe('Suggestions', () => {
  it('renders suggestions', () => {
    const markup = shallow(<Suggestions suggestions={['Ma27_2016']} />);
    expect(markup.prop('bsStyle')).to.equal('warning');
    expect(markup.find('p Translate').prop('content')).to.equal('pages.portal.create_account.suggestions');
    expect(markup.find('ul li').contains('Ma27_2016')).to.equal(true);
  });

  it('renders nothing if no suggestions are provided', () => {
    expect(shallow(<Suggestions suggestions={[]} />).contains('ul')).to.equal(false);
  });

  it('handles state change', () => {
    const markup = shallow(<Suggestions suggestions={['Ma27_2016']} />);
    expect(markup.find('ul li').contains('Ma27_2016')).to.equal(true);
    markup.setProps({ suggestions: ['Ma27_2'] });
    expect(markup.find('ul li').contains('Ma27_2016')).to.equal(false);
    expect(markup.find('ul li').contains('Ma27_2')).to.equal(true);
  })
});
